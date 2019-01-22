<?php
/**
 *
 * @author Marco Petrini <marco@bhima.eu>
 * @created 15/05/14 12:35 PM
 */

namespace pcrt\latex;

use Yii;
use yii\base\Component;
use yii\web\Response;
use yii\web\ResponseFormatterInterface;

/**
 * Latex2PdfResponseFormatter formats the given Latex into a PDF response content.
 *
 * It is used by [[Response]] to format response data.
 *
 * @author Marco Petrini <marco@bhima.eu>
 * @since 2.0
 */
class Latex2PdfResponseFormatter extends Component implements ResponseFormatterInterface
{

	public $latexbin = "/usr/local/bin/pdflatex";
	public $outputdir = \Yii::getAlias('@webroot');

	public $options = [];

	/**
	 * @var Closure function($mpdf, $data){}
	 */
	public $beforeRender;

	/**
	 * Formats the specified response.
	 *
	 * @param Response $response the response to be formatted.
	 */
	public function format($response)
	{
		$response->getHeaders()->set('Content-Type', 'application/pdf');
		$response->content = $this->formatPdf($response);
	}

	/**
	 * Formats response Latex in PDF
	 *
	 * @param Response $response
	 */
	protected function formatPdf($response)
	{
		\Yii::trace($response->data);
		$temp = tmpfile();
		fwrite($temp, $response->data);
		fclose($temp);
		$outputdir = "";
		shell_exec($latexbin . " " . $temp . "-alt-on-error -output-directory " . $output);

	}
}
