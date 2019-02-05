<?php
namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\Cookie;
use yii\base\Module;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2FileManager\helpers\FileWebHelper;
use DmitriiKoziuk\yii2Shop\forms\cart\CartProductInputForm;
use DmitriiKoziuk\yii2Shop\forms\cart\CheckoutForm;
use DmitriiKoziuk\yii2Shop\services\cart\CartService;
use DmitriiKoziuk\yii2Shop\services\cart\CartWebService;
use DmitriiKoziuk\yii2Shop\exceptions\HackException;
use DmitriiKoziuk\yii2Shop\exceptions\cart\AddProductToCartException;

final class CartController extends Controller
{
    /**
     * @var CartService
     */
    private $_cartService;

    /**
     * @var CartWebService
     */
    private $_cartWebService;

    /**
     * @var FileWebHelper
     */
    private $_fileWebHelper;

    public function __construct(
        string $id,
        Module $module,
        CartService $cartService,
        CartWebService $cartWebService,
        FileWebHelper $fileWebHelper,
        array $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->_cartService = $cartService;
        $this->_cartWebService = $cartWebService;
        $this->_fileWebHelper = $fileWebHelper;
    }

    /**
     * @return string
     */
    public function actionView()
    {
        $cookies = Yii::$app->request->getCookies();
        if (! $cookies->has('cart')) {
            return $this->render('empty');
        }
        $cartKey = $cookies->getValue('cart');
        try {
            $cart = $this->_cartWebService->getCartByKey($cartKey);
        } catch (EntityNotFoundException $e) {
            Yii::$app->response->cookies->remove('cart');
            return $this->redirect(['cart/view']);
        }
        return $this->render('view', [
            'cart' => $cart,
            'fileWebHelper' => $this->_fileWebHelper,
        ]);
    }

    public function actionUpdate()
    {
        $cookies = Yii::$app->request->getCookies();
        if ($cookies->has('cart') && Yii::$app->request->post('quantity')) {
            try {
                $this->_cartService->updateProductQuantity(
                    Yii::$app->request->cookies->get('cart'),
                    Yii::$app->request->post('quantity')
                );
            } catch (\Throwable $e) {
                //TODO: log error.
            }
        }
        return $this->redirect(['cart/view']);
    }

    public function actionRemoveProduct(int $id)
    {
        $cookies = Yii::$app->request->getCookies();
        if ($cookies->has('cart')) {
            try {
                $leftProductInCart = $this->_cartService->removeProductFromCart(
                    Yii::$app->request->cookies->get('cart'),
                    $id
                );
                if ($leftProductInCart == 0) {
                    Yii::$app->response->cookies->remove('cart');
                }
            } catch (\Throwable $e) {
                //TODO: log error.
            }
        }
        return $this->redirect(['cart/view']);
    }

    /**
     * @param int $product
     * @return \yii\web\Response
     */
    public function actionAddProduct(int $product)
    {
        try {
            $cartProductInputForm = new CartProductInputForm();
            $cartProductInputForm->productSkuId = $product;
            $cookies = Yii::$app->request->getCookies();
            if ($cookies->has('cart')) {
                $cartProductInputForm->cartKey = $cookies->getValue('cart');
            }
            $cart = $this->_cartService->addProductToCart($cartProductInputForm);
            if (! $cookies->has('cart')) {
                Yii::$app->response->cookies->add(new Cookie([
                    'name'  => 'cart',
                    'value' => $cart->key,
                ]));
            }
        } catch (HackException $e) {
            Yii::$app->response->cookies->remove('cart');
        } catch (AddProductToCartException $e) {
            //TODO something goes wrong.
        }
        return $this->redirect(['cart/view']);
    }

    public function actionCheckout()
    {
        $cookies = Yii::$app->request->getCookies();
        if ($cookies->has('cart')) {
            $cartKey = $cookies->get('cart');
            $checkoutForm = new CheckoutForm();
            if (
                Yii::$app->request->isPost &&
                $checkoutForm->load(Yii::$app->request->post()) &&
                $checkoutForm->validate()
            ) {
                $this->_cartService->checkout($cartKey, $checkoutForm);
                Yii::$app->response->cookies->remove('cart');
                return $this->redirect(['cart/thanks']);
            } else {
                return $this->render('checkout', [
                    'checkoutForm' => $checkoutForm,
                ]);
            }
        } else {
            return $this->redirect(['cart/view']);
        }
    }

    public function actionThanks()
    {
        return $this->renderContent('Thanks');
    }
}