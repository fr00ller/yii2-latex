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
	public $outputdir = "";

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
		$tmpfile_name = uniqid();
		$tmpfile_path = getcwd()."/".$tmpfile_name;

		$outputdir = getcwd();
		$fp = fopen($tmpfile_path, 'w+');
		fwrite($fp, $response->data);
		fclose($fp);
		$cmd = $this->latexbin . " " . $tmpfile_path . " -alt-on-error -output-directory " . $outputdir;

		// Refator to process
		shell_exec($cmd);
		\Yii::trace("EXEC: ".$cmd);
		unlink(getcwd()."/".$tmpfile_name.".log");
		unlink(getcwd()."/".$tmpfile_name.".aux");
		if(file_exists(getcwd()."/".$tmpfile_name.".pdf")){
			$pdf = file_get_contents(getcwd()."/".$tmpfile_name.".pdf");
			unlink(getcwd()."/".$tmpfile_name.".pdf");
			return $pdf;
		}
	}
}
