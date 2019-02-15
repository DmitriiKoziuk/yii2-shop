<?php
namespace DmitriiKoziuk\yii2Shop\services\cart;

use Yii;
use yii\base\Exception;
use yii\db\Connection;

use DmitriiKoziuk\yii2Base\services\DBActionService;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotValidException;
use DmitriiKoziuk\yii2Base\exceptions\EntitySaveException;
use DmitriiKoziuk\yii2Base\exceptions\EntityNotFoundException;
use DmitriiKoziuk\yii2Base\exceptions\ExternalComponentException;

use DmitriiKoziuk\yii2Shop\data\order\OrderStage;
use DmitriiKoziuk\yii2Shop\forms\cart\CartProductInputForm;
use DmitriiKoziuk\yii2Shop\forms\cart\CheckoutForm;
use DmitriiKoziuk\yii2Shop\repositories\CartRepository;
use DmitriiKoziuk\yii2Shop\repositories\CartProductRepository;
use DmitriiKoziuk\yii2Shop\repositories\OrderRepository;
use DmitriiKoziuk\yii2Shop\repositories\OrderStageLogRepository;
use DmitriiKoziuk\yii2Shop\repositories\CustomerRepository;
use DmitriiKoziuk\yii2Shop\repositories\ProductSkuRepository;
use DmitriiKoziuk\yii2Shop\entities\ProductSku;
use DmitriiKoziuk\yii2Shop\entities\Cart;
use DmitriiKoziuk\yii2Shop\entities\CartProduct;
use DmitriiKoziuk\yii2Shop\entities\Order;
use DmitriiKoziuk\yii2Shop\entities\OrderStageLog;
use DmitriiKoziuk\yii2Shop\entities\Customer;
use DmitriiKoziuk\yii2Shop\exceptions\HackException;
use DmitriiKoziuk\yii2Shop\exceptions\cart\CartNotFoundException;
use DmitriiKoziuk\yii2Shop\exceptions\product\ProductSkuNotFoundException;
use DmitriiKoziuk\yii2Shop\exceptions\product\ProductSkuSitePriceNotSet;
use DmitriiKoziuk\yii2Shop\exceptions\cart\CartProductsNotFoundException;
use DmitriiKoziuk\yii2Shop\exceptions\cart\AddProductToCartException;
use DmitriiKoziuk\yii2Shop\exceptions\InputDataNotValidException;

class CartService extends DBActionService
{
    /**
     * @var CartRepository
     */
    private $_cartRepository;

    /**
     * @var CartProductRepository
     */
    private $_cartProductRepository;

    /**
     * @var CustomerRepository
     */
    private $_customerRepository;

    /**
     * @var OrderRepository
     */
    private $_orderRepository;

    /**
     * @var OrderStageLogRepository
     */
    private $_orderStageLogRepository;

    /**
     * @var ProductSkuRepository
     */
    private $_productSkuRepository;

    /**
     * @var OrderStage
     */
    private $_orderStageData;

    public function __construct(
        CartRepository $cartRepository,
        CartProductRepository $cartProductRepository,
        CustomerRepository $customerRepository,
        OrderRepository $orderRepository,
        OrderStageLogRepository $orderStageLogRepository,
        ProductSkuRepository $productSkuRepository,
        Connection $db = null
    ) {
        parent::__construct($db);
        $this->_cartRepository = $cartRepository;
        $this->_cartProductRepository = $cartProductRepository;
        $this->_customerRepository = $customerRepository;
        $this->_orderRepository = $orderRepository;
        $this->_orderStageLogRepository = $orderStageLogRepository;
        $this->_productSkuRepository = $productSkuRepository;
        $this->_orderStageData = new OrderStage();
    }

    /**
     * @param CartProductInputForm $cartProductInputForm
     * @return Cart
     * @throws AddProductToCartException
     * @throws HackException
     */
    public function addProductToCart(CartProductInputForm $cartProductInputForm): Cart
    {
        $this->beginTransaction();
        try {
            try {
                if (! $cartProductInputForm->validate()) {
                    throw new InputDataNotValidException('Data not valid.');
                }
                /** @var ProductSku $productSku */
                $productSku = $this->_productSkuRepository->getById($cartProductInputForm->productSkuId);
                if (empty($productSku)) {
                    throw new ProductSkuNotFoundException('Product sku not found.');
                }
                if (empty((float) $productSku->price_on_site)) {
                    throw new ProductSkuSitePriceNotSet('Product sku site price not set.');
                }
                if (empty($cartProductInputForm->cartKey)) {
                    $cart = $this->_createCart(Yii::$app->security->generateRandomString());
                } else {
                    /** @var Cart $cart */
                    $cart = $this->_cartRepository->getByKey($cartProductInputForm->cartKey);
                    if (empty($cart)) {
                        throw new CartNotFoundException('Cart not found.');
                    }
                }
                $this->_addProductSku($cart, $productSku);
                $this->commitTransaction();
                return $cart;
            } catch (InputDataNotValidException | ProductSkuNotFoundException | CartNotFoundException $e) {
                $this->rollbackTransaction();
                $ex = new HackException('Hack exception');
                $ex->addErrors([$e]);
                throw $ex;
            }
        } catch (ExternalComponentException | ProductSkuSitePriceNotSet | Exception $e) {
            $newE = new AddProductToCartException();
            $newE->addErrors([$e]);
            throw $newE;
        }
    }

    /**
     * @param string $cartKey
     * @param int $productSkuId
     * @return int number of left product in cart
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function removeProductFromCart(string $cartKey, int $productSkuId): int
    {
        $this->beginTransaction();
        try {
            $cart = $this->_cartRepository->getByKey($cartKey);
            if (empty($cart)) {
                throw new CartNotFoundException('Cart not found.');
            }
            $cartProducts = $this->_cartProductRepository->getCartProducts($cart->id);
            if (empty($cartProducts)) {
                throw new CartProductsNotFoundException('Cart products found.');
            }
            $searchCartProductKey = null;
            $searchCartProductEntity = null;
            foreach ($cartProducts as $key => $cartProduct) {
                if ($cartProduct->product_sku_id == $productSkuId) {
                    $searchCartProductKey = $key;
                    $searchCartProductEntity = $cartProduct;
                }
            }
            if (empty($searchCartProductEntity)) {
                throw new EntityNotFoundException('Cart product found.');
            }
            $this->_cartProductRepository->delete($searchCartProductEntity);
            unset($cartProducts[ $searchCartProductKey ]);
            if (count($cartProducts) == 0) {
                $this->_deleteCart($cart);
            }
            $this->commitTransaction();
            return count($cartProducts);
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param string $cartKey
     * @param array $quantity
     * @throws ExternalComponentException
     * @throws \Throwable
     */
    public function updateProductQuantity(string $cartKey, array $quantity): void
    {
        $this->beginTransaction();
        try {
            $cart = $this->_cartRepository->getByKey($cartKey);
            if (empty($cart)) {
                throw new CartNotFoundException('Cart not found.');
            }
            $cartProducts = $this->_cartProductRepository->getCartProducts($cart->id);
            if (empty($cartProducts)) {
                throw new CartProductsNotFoundException('Cart products found.');
            }
            foreach ($cartProducts as $cartProduct) {
                if (isset($quantity[ $cartProduct->product_sku_id ])) {
                    $cartProduct->quantity = (int) $quantity[ $cartProduct->product_sku_id ];
                    $this->_cartRepository->save($cartProduct);
                }
            }
            $this->commitTransaction();
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    public function checkout(string $cartKey, CheckoutForm $checkoutForm): void
    {
        $this->beginTransaction();
        try {
            $cart = $this->_cartRepository->getByKey($cartKey);
            if (empty($cart)) {
                throw new CartNotFoundException('Cart not found.');
            }
            $customer = $this->_customerRepository->getByPhoneNumber($checkoutForm->phone_number);
            if (empty($customer)) {
                $customer = $this->_createCustomer($checkoutForm);
            }
            $this->_addCustomerToCart($customer, $cart);
            $this->_createOrder($cart);
            $this->commitTransaction();
        } catch (\Throwable $e) {
            $this->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * @param string $cartKey
     * @return Cart
     * @throws ExternalComponentException
     */
    private function _createCart(string $cartKey): Cart
    {
        try {
            $cart = new Cart();
            $cart->key = $cartKey;
            $this->_cartRepository->save($cart);
            return $cart;
        } catch (EntityNotValidException | EntitySaveException $e) {
            throw new ExternalComponentException('Cant create cart.');
        }
    }

    /**
     * @param Cart $cart
     * @throws \DmitriiKoziuk\yii2Base\exceptions\EntityDeleteException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    private function _deleteCart(Cart $cart): void
    {
        $this->_cartRepository->delete($cart);
    }

    /**
     * @param Cart $cart
     * @param ProductSku $productSku
     * @param int $productQuantity
     * @return CartProduct
     * @throws ExternalComponentException
     */
    private function _addProductSku(
        Cart $cart,
        ProductSku $productSku,
        int $productQuantity = 1
    ): CartProduct {
        try {
            $relation = $this->_cartProductRepository->getRelation((int) $cart->id, (int) $productSku->id);
            if (empty($relation)) {
                $relation = new CartProduct();
                $relation->cart_id = $cart->id;
                $relation->product_sku_id = $productSku->id;
                $relation->quantity = $productQuantity;
                $this->_cartProductRepository->save($relation);
            } else {
                $relation->quantity += 1;
                $this->_cartProductRepository->save($relation);
            }
            return $relation;
        } catch (EntityNotValidException | EntitySaveException $e) {
            throw new ExternalComponentException('Cant add product to cart.');
        }
    }

    /**
     * @param CheckoutForm $checkoutForm
     * @return Customer
     * @throws ExternalComponentException
     */
    private function _createCustomer(CheckoutForm $checkoutForm)
    {
        try {
            $customer = new Customer();
            $customer->phone_number = $checkoutForm->phone_number;
            $customer->first_name = $checkoutForm->first_name;
            $this->_customerRepository->save($customer);
            return $customer;
        } catch (EntityNotValidException | EntitySaveException $e) {
            throw new ExternalComponentException('Cant create customer.');
        }
    }

    /**
     * @param Customer $customer
     * @param Cart $cart
     * @throws ExternalComponentException
     */
    private function _addCustomerToCart(Customer $customer, Cart $cart)
    {
        try {
            $cart->customer_id = $customer->id;
            $this->_customerRepository->save($cart);
        } catch (EntityNotValidException | EntitySaveException $e) {
            throw new ExternalComponentException('Cant create customer.');
        }
    }

    /**
     * @param Cart $cart
     * @throws ExternalComponentException
     */
    private function _createOrder(Cart $cart)
    {
        try {
            $order = new Order();
            $order->id = $cart->id;
            $this->_orderRepository->save($order);
            $orderStageLog = new OrderStageLog();
            $orderStageLog->order_id = $order->id;
            $orderStageLog->stage_id = OrderStage::ORDER_CREATED;
            $this->_orderStageLogRepository->save($orderStageLog);
        } catch (EntityNotValidException | EntitySaveException $e) {
            throw new ExternalComponentException('Cant create order.');
        }
    }
}