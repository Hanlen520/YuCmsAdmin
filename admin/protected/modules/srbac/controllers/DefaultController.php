<?php
/**
 * The default srbac controller
 */
class DefaultController extends CController {
  /**
   * The default action if no route is specified
   */
	public function actionIndex() {

		//column_roles
		$this->layout='//layouts/column_roles';
		//$this->render('index');
		$this->redirect(array('authitem/frontpage'));
	}
 }