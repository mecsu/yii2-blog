<?php

namespace wdmg\blog\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use wdmg\blog\models\Blog;
use yii\data\ActiveDataProvider;

/**
 * DefaultController implements actions for Blog model.
 */
class DefaultController extends Controller
{


    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {

        // Set a default layout
        $this->layout = $this->module->blogLayout;

        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    /**
     * View of blog item.
     *
     * @param string $blog aliases of searching blog.
     * @return mixed
     * @see Blog::$alias
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Blog::find(),
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', [
            'route' => $route,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    /**
     * View of blog item.
     *
     * @param string $blog aliases of searching blog.
     * @param boolean $draft flag of status searching blog.
     * @return mixed
     * @see Blog::$alias
     */
    public function actionView($alias, $draft = false)
    {

        // Search page model with alias
        $model = $this->findModel($alias, $draft);
        $route = $model->getRoute();

        // Check probably need redirect to new URL
        if (isset(Yii::$app->redirects)) {
            if (Yii::$app->redirects->check(Yii::$app->request->getUrl()))
                return Yii::$app->redirects->check(Yii::$app->request->getUrl());
        }

        // Separate route from request URL
        if (is_null($route) && preg_match('/^([\/]+[A-Za-z0-9_\-\_\/]+[\/])*([A-Za-z0-9_\-\_]*)/i', Yii::$app->request->url,$matches)) {
            if ($alias == $matches[2])
                $route = rtrim($matches[1], '/');
        }

        // If route is root
        if (empty($route))
            $route = '/';

        return $this->render('view', [
            'route' => $route,
            'model' => $model
        ]);
    }

    /**
     * Finds the Page model based on alias value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param string $alias
     * @return Page model
     * @throws NotFoundHttpException if the model not exist or not published
     */
    protected function findModel($alias, $isDraft = false)
    {

        $status = Blog::POST_STATUS_PUBLISHED;
        if ($isDraft)
            $status = Blog::POST_STATUS_DRAFT;

        $model = Blog::find()->where([
            'alias' => $alias,
            'status' => $status,
        ])->one();

        if (!is_null($model))
            return $model;
        else
            throw new NotFoundHttpException();

    }
}