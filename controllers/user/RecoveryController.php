<?php

namespace app\controllers\user;

use Yii;
use app\models\RecoveryForm;
use dektrium\user\models\Token;
use yii\web\NotFoundHttpException;

use dektrium\user\controllers\RecoveryController as BaseRecoveryController;

class RecoveryController extends BaseRecoveryController
{
    /**
     * Shows page where user can request password recovery.
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionRequest()
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException();
        }

        /** @var RecoveryForm $model */
        $model = Yii::createObject([
            'class'    => RecoveryForm::className(),
            'scenario' => 'request',
        ]);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->request->post()) && $model->sendRecoveryMessage()) {
            return $this->render('/message', [
                'title'  => Yii::t('user', 'Recovery message sent'),
                'module' => $this->module,
            ]);
        }

        return $this->render('request', [
            'model' => $model,
        ]);
    }

    /**
     * Displays page where user can reset password.
     *
     * @param int    $id
     * @param string $code
     *
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionReset($id, $code)
    {
        if (!$this->module->enablePasswordRecovery) {
            throw new NotFoundHttpException();
        }

        /** @var Token $token */
        $token = $this->finder->findToken(['user_id' => $id, 'code' => $code, 'type' => Token::TYPE_RECOVERY])->one();

        if ($token === null || $token->isExpired || $token->user === null) {
            Yii::$app->session->setFlash('danger', Yii::t('user', 'Recovery link is invalid or expired. Please try requesting a new one.'));

            return $this->render('/message', [
                'title'  => Yii::t('user', 'Invalid or expired link'),
                'module' => $this->module,
            ]);
        }

        /** @var RecoveryForm $model */
        $model = Yii::createObject([
            'class'    => RecoveryForm::className(),
            'scenario' => 'reset',
        ]);

        $this->performAjaxValidation($model);

        if ($model->load(Yii::$app->getRequest()->post()) && $model->resetPassword($token)) {
            return $this->render('/message', [
                'title'  => Yii::t('user', 'Password has been changed'),
                'module' => $this->module,
            ]);
        }

        return $this->render('reset', [
            'model' => $model,
        ]);
    }
}