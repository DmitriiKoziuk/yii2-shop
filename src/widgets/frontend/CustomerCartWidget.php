<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\widgets\frontend;

use Yii;
use yii\base\Widget;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2Shop\repositories\CartRepository;
use DmitriiKoziuk\yii2Shop\repositories\CartProductRepository;

class CustomerCartWidget extends Widget
{
    /**
     * @var CartRepository
     */
    private $cartRepository;

    /**
     * @var CartProductRepository
     */
    private $cartProductRepository;

    public function __construct(
        CartRepository $cartRepository,
        CartProductRepository $cartProductRepository,
        $config = []
    ) {
        parent::__construct($config);
        $this->cartRepository = $cartRepository;
        $this->cartProductRepository = $cartProductRepository;
    }

    public function run()
    {
        $cookies = Yii::$app->request->getCookies();
        if (! $cookies->has('cart')) {
            return $this->render('customer-cart-empty');
        }
        $cartKey = $cookies->getValue('cart');
        try {
            $cart = $this->cartRepository->getByKey($cartKey);
            $cartProducts = $this->cartProductRepository->getCartProducts($cart->id);
            $totalProducts = 0;
            $totalPrice = 0;
            foreach ($cartProducts as $cartProduct) {
                $totalProducts += $cartProduct->quantity;
                $totalPrice += $cartProduct->productSku->getSellPrice() * $cartProduct->quantity;
            }
        } catch (EntityNotFoundException $e) {
            Yii::$app->response->cookies->remove('cart');
            return $this->redirect(['cart/view']);
        }
        return $this->render('customer-cart', [
            'cart' => $cart,
            'totalProducts' => $totalProducts,
            'totalPrice' => $totalPrice,
        ]);
    }
}