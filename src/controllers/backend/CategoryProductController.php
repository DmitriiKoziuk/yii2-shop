<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\backend;

use DmitriiKoziuk\yii2Shop\repositories\CategoryProductSkuRepository;
use Yii;
use yii\web\Controller;
use yii\web\BadRequestHttpException;
use DmitriiKoziuk\yii2Shop\repositories\ProductRepository;

class CategoryProductController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var CategoryProductSkuRepository
     */
    private $categoryProductSkuRepository;

    public function __construct(
        $id,
        $module,
        ProductRepository $productRepository,
        CategoryProductSkuRepository $categoryProductSkuRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->productRepository = $productRepository;
        $this->categoryProductSkuRepository = $categoryProductSkuRepository;
    }

    public function actionIndex()
    {
        return $this->renderContent('Hello');
    }

    /**
     * @param int $id
     * @return string
     * @throws BadRequestHttpException
     * @throws \Throwable
     */
    public function actionSort(int $id)
    {
        $product = $this->productRepository->getById($id);
        if (empty($product)) {
            throw new BadRequestHttpException("Product with id '{$id}' do not exist.");
        }
        if (Yii::$app->request->isPost && !empty(Yii::$app->request->post('productSkuList'))) {
            $productSkuList = Yii::$app->request->post('productSkuList');
            foreach ($productSkuList as $skuId => $categories) {
                foreach ($categories as $categoryId => $sort) {
                    $this->categoryProductSkuRepository->updateSort($categoryId, $skuId, (int) $sort);
                }
            }
        }
        return $this->render('sort', [
            'product' => $product,
        ]);
    }
}