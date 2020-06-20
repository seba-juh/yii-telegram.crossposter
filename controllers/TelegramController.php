<?php

namespace app\controllers;

use app\controllers\CustomController\CustomController;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Exception;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class TelegramController extends CustomController
{
	public $modelClass = 'app\models\User';
	public $enableCsrfValidation = false;
	public $token = "973605780:AAEUuSr2fcnapRvAtHjoiRKjbNyFnrZx5Ek";
	public function behaviors()
	{
		return [
		];
	}
	private function findInWiki($search){
		$url = 'http://en.wikipedia.org/w/api.php?action=query&prop=extracts&format=json&explaintext=&titles='.$search;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		$res = curl_exec($ch);
		curl_close($ch);
		return $res;
	}
	private function getContent($wiki_res){
		$json = json_decode($wiki_res);

		$content = $json->query->pages;
		$wiki_id = '';
		foreach ($content as $key => $value) {
			$wiki_id = $key;
		}
		$content = $content->$wiki_id->extract;
		return $content;
	}
	private function checkMessage($post){
		$message = $post["message"]["text"];
		foreach($message as $key=>$char){
			if($char == "/" || $char =="&"){
				$message="";
				break;
			}
			elseif($char == " "){
				$message[$key]="_";
			}
		}
		return $message;
	}
	public function actionGetMessages()
	{
		$bot = new \TelegramBot\Api\BotApi($this->token);
		$post = Yii::$app->request->post();
		if(empty($post)) return;
		if(isset($post["message"]["text"])){
			if($post["message"]["text"]=="/start") {
				$bot->sendMessage($post["message"]["chat"]["id"], "Hello, welcome to wiki bot, send me what you want to know. Send it in english please!");
			} else {
				$content = $this->getContent($this->findInWiki("Dog"));
				if($this->checkMessage($post["message"])==""){
					$bot->sendMessage($post["message"]["chat"]["id"], "Wrong text, sorry");
				} else {
					$bot->sendMessage($post["message"]["chat"]["id"], $content);
				}
			}
		} else {
			$bot->sendMessage($post["message"]["chat"]["id"], "Sorry, i don't understand you...");
		}
	}
}
