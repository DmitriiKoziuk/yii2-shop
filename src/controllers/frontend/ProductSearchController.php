<?php declare(strict_types=1);

namespace DmitriiKoziuk\yii2Shop\controllers\frontend;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use DmitriiKoziuk\yii2Shop\repositories\ProductRepository;
use DmitriiKoziuk\yii2Shop\data\product\ProductSearchParams;
use DmitriiKoziuk\yii2Shop\entityViews\ProductEntityView;

class ProductSearchController extends Controller
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        $id,
        $module,
        ProductRepository $productRepository,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->productRepository = $productRepository;
    }

    public function actionJsonResponse(string $search)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $searchResponse = $this->productRepository->searchByName(new ProductSearchParams([
            'name' => $search,
        ]), 5);

        $list = [];
        foreach ($searchResponse->getItems() as $entity) {
            $list[] = new ProductEntityView($entity);
        }

        $returnList = [
            'totalCount' => $searchResponse->getTotalCount(),
            'products' => [],
        ];
        foreach ($list as $item) {
            $returnList['products'][ $item->getId() ] = [
                'id' => $item->getId(),
                'name' => $item->getFullName(),
                'url' => $item->getUrl(),
                'image' => $item->getMainImage()->getThumbnail(100, 100, 65),
            ];
        }

        return $returnList;
    }
}