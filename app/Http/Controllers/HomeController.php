<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Jobs\SendPushNotification;
use App\Token;
use App\Translation;
use App\Word;
use Davibennun\LaravelPushNotification\Facades\PushNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Validator;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function wordsImportShow()
    {
        return view('words.import');
    }

    private function import()
    {

    }

    public function upload(Request $request)
    {
        $language = $request->get('language');

        $filename = $request->file('importfile')->getClientOriginalName();
        $dirname = storage_path() . '/import/';
        $request->file('importfile')->move($dirname, $filename);

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($dirname  . $filename);
        $worksheet = $spreadsheet->getActiveSheet();

        $maxRowIndex = $worksheet->getHighestRow();

        $updated = 0;
        $added = 0;

        for ($i = 1; $i <= $maxRowIndex; $i++) {
            /** @var \PhpOffice\PhpSpreadsheet\Cell $word */
            $word = $worksheet->getCellByColumnAndRow(0, $i);
            /** @var \PhpOffice\PhpSpreadsheet\Cell $translation */
            $translation = $worksheet->getCellByColumnAndRow(1, $i);

            $wordFormatted = $this->encodeExcelCellAsHtml($word);
            $wordFormatted = preg_replace('%[0-9]+\.[\s\x{00a0}]*%siu', '', $wordFormatted);

            $word = $word->getValue();
            $word = preg_replace('%[0-9]+\.[\s\x{00a0}]*%siu', '', $word);

            $translation = $this->encodeExcelCellAsHtml($translation);

            if ($word && $translation) {
                if ($wordObj = Word::where('word', $word)->where('language', $language)->first()) {

                    $wordObj->translation = $translation;
                    $wordObj->word_formatted = $wordFormatted;
                    $wordObj->save();
                    $updated++;
                } else {
                    $wordObj = Word::create([
                        'word' => $word,
                        'word_formatted' => $wordFormatted,
                        'language' => $language,
                        'translation' => $translation
                    ]);

                    $added++;
                }
            }
        }

        Session::flash('added', $added);
        Session::flash('updated', $updated);
//        die();
        return Redirect::to('/import');

    }

    protected function encodeExcelCellAsHtml(\PhpOffice\PhpSpreadsheet\Cell $cell) {

        $htmlValue = $cell->getValue();
        if ($cell->getValue() instanceof \PhpOffice\PhpSpreadsheet\RichText) {
            $htmlValue = '';
            $isRelated = false;
            $isRelated2 = false;
            foreach ($cell->getValue()->getRichTextElements() as $element) {

                if($isRelated || $isRelated2) {
                    if ((!preg_match('/\s/',$element->getText()))
                        && trim($element->getText())!='см.' && trim($element->getText())!='ср.'
                        && (!preg_match('/\\d/', trim($element->getText())) > 0)
                        && strlen(trim($element->getText()))>2) {
//                        echo '<pre>';
//                        print_r(strlen(trim($element->getText())));
//                        print_r($element);
//                        echo '</pre>';
                        $isRelated = false;
                    }
                }

                if(trim($element->getText())=='см.')
                    $isRelated = true;
                if(trim($element->getText())=='ср.')
                    $isRelated2 = true;


                if ($element instanceof \PhpOffice\PhpSpreadsheet\RichText\Run) {
                    /** @var $element \PhpOffice\PhpSpreadsheet\RichText\Run */

                    $template = '%s';

                    if ($element->getFont()->getBold()) {
                        $template = sprintf('<strong>%s</strong>', $template);
                    }

                    if ($element->getFont()->getItalic()) {
                        $template = sprintf('<i>%s</i>', $template);
                    }

                    if ($element->getFont()->getSuperScript()) {
                        $template = sprintf('<sup>%s</sup>', $template);
                    }

                    if ($element->getFont()->getSubScript()) {
                        $template = sprintf('<sub>%s</sub>', $template);
                    }

                    if ($element->getFont()->getUnderline() != \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_NONE) {
                        $template = sprintf('<u>%s</u>', $template);

                        if ($element->getFont()->getUnderline() != \PhpOffice\PhpSpreadsheet\Style\Font::UNDERLINE_SINGLE) {
                            Log::warning(
                              'unimplemented underline style ' . $element->getFont()->getUnderline()
                            );
                        }
                    }

                    if ($element->getFont()->getStrikethrough()) {
                        $template = sprintf('<s>%s</s>', $template);
                    }

                    // whitespaces and unicode non breaking spaces as single
                    $text = preg_replace('%[\s\x{00a0}]%siu', ' ', $element->getText());
                    $htmlValue .= sprintf($template, $text);
                } else {
                    $htmlValue .= $element->getText();
                }
            }
        }

        return $htmlValue;
    }

}
