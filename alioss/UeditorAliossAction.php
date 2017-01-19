<?php
namespace ijackwu\ueditor\alioss;

use Yii;
use yii\helpers\ArrayHelper;
use kucha\ueditor\UEditorAction;
class UeditorAliossAction extends UEditorAction
{
    /**
     * 上传
     * @return array
     */
    public function actionUpload()
    {
        $base64 = "upload";
        switch (htmlspecialchars($_GET['action'])) {
            case 'uploadimage':
                $config = array(
                    "pathRoot" => ArrayHelper::getValue($this->config, "imageRoot", $_SERVER['DOCUMENT_ROOT']),
                    "pathFormat" => $this->config['imagePathFormat'],
                    "maxSize" => $this->config['imageMaxSize'],
                    "allowFiles" => $this->config['imageAllowFiles']
                );
                $fieldName = $this->config['imageFieldName'];
                break;
            case 'uploadscrawl':
                $config = array(
                    "pathRoot" => ArrayHelper::getValue($this->config, "scrawlRoot", $_SERVER['DOCUMENT_ROOT']),
                    "pathFormat" => $this->config['scrawlPathFormat'],
                    "maxSize" => $this->config['scrawlMaxSize'],
                    "allowFiles" => $this->config['scrawlAllowFiles'],
                    "oriName" => "scrawl.png"
                );
                $fieldName = $this->config['scrawlFieldName'];
                $base64 = "base64";
                break;
            case 'uploadvideo':
                $config = array(
                    "pathRoot" => ArrayHelper::getValue($this->config, "videoRoot", $_SERVER['DOCUMENT_ROOT']),
                    "pathFormat" => $this->config['videoPathFormat'],
                    "maxSize" => $this->config['videoMaxSize'],
                    "allowFiles" => $this->config['videoAllowFiles']
                );
                $fieldName = $this->config['videoFieldName'];
                break;
            case 'uploadfile':
            default:
                $config = array(
                    "pathRoot" => ArrayHelper::getValue($this->config, "fileRoot", $_SERVER['DOCUMENT_ROOT']),
                    "pathFormat" => $this->config['filePathFormat'],
                    "maxSize" => $this->config['fileMaxSize'],
                    "allowFiles" => $this->config['fileAllowFiles']
                );
                $fieldName = $this->config['fileFieldName'];
                break;
        }
        /* 生成上传实例对象并完成上传 */

        $up = new Uploader($fieldName, $config, $base64);
        /**
         * 得到上传文件所对应的各个参数,数组结构
         * array(
         *     "state" => "",          //上传状态，上传成功时必须返回"SUCCESS"
         *     "url" => "",            //返回的地址
         *     "title" => "",          //新文件名
         *     "original" => "",       //原始文件名
         *     "type" => ""            //文件类型
         *     "size" => "",           //文件大小
         * )
         */

        /* 返回数据 */
        return $up->getFileInfo();
    }

	/**
	 * 抓取远程图片
	 * @return array
	 */
	protected function actionCrawler()
	{
		/* 上传配置 */
		$config = array(
			"pathFormat" => $this->config['catcherPathFormat'],
			"maxSize" => $this->config['catcherMaxSize'],
			"allowFiles" => $this->config['catcherAllowFiles'],
			"oriName" => "remote.png",
			"pathRoot" => $this->config['fileRoot'],
		);
		$fieldName = $this->config['catcherFieldName'];

		/* 抓取远程图片 */
		$list = array();
		if (isset($_POST[$fieldName])) {
			$source = $_POST[$fieldName];
		} else {
			$source = $_GET[$fieldName];
		}
		foreach ($source as $imgUrl) {
			$item = new Uploader($imgUrl, $config, "remote");
			$info = $item->getFileInfo();
			array_push($list, [
				"state" => $info["state"],
				"url" => $info["url"],
				"size" => $info["size"],
				"title" => htmlspecialchars($info["title"]),
				"original" => htmlspecialchars($info["original"]),
				"source" => htmlspecialchars($imgUrl)
			]);
		}

		/* 返回抓取数据 */
		return [
			'state' => count($list) ? 'SUCCESS' : 'ERROR',
			'list' => $list
		];
	}

}