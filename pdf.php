<?php

/**
 * @package     local_courseanalytics
 * @author      Robert Tyrone Cullen
 * @var stdClass $plugin
 */

require_once(__DIR__ . '/../../config.php');
require_login();
$context = context_system::instance();
require_capability('local/courseanalytics:courseanalytics', $context);
use local_courseanalytics\lib;
$lib = new lib();
$p = 'local_courseanalytics';

//Include tcpdf file
require_once($CFG->libdir.'/filelib.php');
require_once($CFG->libdir.'/tcpdf/tcpdf.php');

//Extends the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF{
    public function Header(){
        $this->Image('classes/img/logo.png', $this->GetPageWidth() - 32, $this->GetPageHeight() - 22, 30, 20, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
    }
    public function Footer(){
        //Set position from botton
        $this->setY(-15);
        //Set font
        $this->SetFont('Times', 'B', 12);
        //Page number
        $this->Cell(0, 0, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

//Create new pdf
$pdf = new MyPDF('P','mm','A4');

$cheight = 10;
$font = 'Times';
$cmykdual = [array(21, 27, 0, 0), array(4, 6, 0, 0)];
$titlesize = 32;
$sixteen = 16;
$tablehead = 13;
$tabletext = 12;

//Front Page
$pdf->setPrintHeader(false);
$pdf->AddPage('L');
$pdf->setFont($font, 'B', 64);
$pdf->Image('classes/img/logo.png', ($pdf->GetPageWidth() / 2 )- 27, 5, 54, 36, 'PNG', '', '', true, 150, '', false, false, 0, false, false, false);
$pdf->Cell(0, 0, '', 0, 0, 'C', 0, '', 0);
$pdf->Ln();
$pdf->Ln();
$pdf->Cell(0, 0, get_string('report_head', $p), 0, 0, 'C', 0, '', 0);
$pdf->setFont($font, '', $titlesize);
$pdf->Ln();
$pdf->Cell(0, 20, get_string('from', $p).': '.date('d/m/Y', strtotime('-8 days')). '  '. get_string('to', $p).': '.date('d/m/Y', strtotime('-1 days')), 0, 0, 'C', 0, '', 0);

//Strings
$cd = get_string('course_d', $p);
$al = get_string('all_l', $p);
$nau = get_string('never_au', $p);
$ef = get_string('enrolments_ftw', $p);
$or = get_string('overall_re', $p);
$c = get_string('course', $p);
$l = get_string('learner', $p);
$sd = get_string('start_d', $p);

//2nd Page
$indexw = 70;
$conheight = 12;
$pdf->setPrintHeader(true);
$pdf->AddPage('L');
$pdf->setFont($font, 'B', $titlesize);
$pdf->Cell(0, 0, get_string('r_index_title', $p).':', 0, 0, 'L', 0, '', 0);
$pdf->setFont($font, '', $sixteen);
$pdf->Ln();
$pdf->Cell(0, $cheight, get_string('r_index_info', $p), 0, 0, 'L', 0, '', 0);
$pdf->setFont($font, 'B', $sixteen);
$pdf->Ln();
$pdf->Cell($indexw, $cheight, get_string('overall_r', $p).':', 0, 0, 'L', 0, '', 0);
$pdf->Ln();
$pdf->setFont($font, '', $sixteen);

$pdf->Cell($indexw, $cheight, '1) '.$cd, 0, 0, 'L', 0, '', 0);
$pdf->Cell(0, $conheight, '- '.get_string('r_course_info', $p), 0, 0, 'L', 0, '', 0);
$pdf->Ln();
$pdf->Cell($indexw, $cheight, '2) '.$al, 0, 0, 'L', 0, '', 0);
$pdf->Cell(0, $conheight, '- '.get_string('r_al_info', $p), 0, 0, 'L', 0, '', 0);
$pdf->Ln();
$pdf->Cell($indexw, $cheight, '3) '.$nau, 0, 0, 'L', 0, '', 0);
$pdf->Cell(0, $conheight, '- '.get_string('r_nau_info', $p), 0, 0, 'L', 0, '', 0);
$pdf->Ln();
$pdf->Cell($indexw, $cheight, '4) '.$ef, 0, 0, 'L', 0, '', 0);
$pdf->Cell(0, $conheight, '- '.get_string('r_pet_info', $p), 0, 0, 'L', 0, '', 0);
$pdf->Ln();

//Add Course info page(s)
$pdf->addPage('L');
$pdf->setFont($font, 'B', $sixteen);
$pdf->Cell(0, 0, $or.': 1', 0, 0, 'L', 0, '', 0);
$pdf->Ln();
//Set the title
$pdf->setFont($font, 'B', $titlesize);
$pdf->Cell(0, 0, $cd, 0, 0, 'C', 0, '', 0);
$pdf->setFont($font, '', $sixteen);
$pdf->Ln();
$pdf->Cell(0, $cheight, get_string('r_course_info_asc', $p), 0, 0, 'C', 0, '', 0);
$pdf->Ln();
//Set the font
$pdf->setFont($font, 'B', $sixteen);
//Add cell to page
$pdf->Cell(275, $cheight, $cd, 1, 0, 'C', 0, '', 0);
//Add new line
$pdf->Ln();
//Add table headers
$length = 275/3;
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($length*.5, $cheight, '#', 1, 0, 'C', 0, '', 0);
$pdf->Cell($length*1.5, $cheight, $c, 1, 0, 'C', 0, '', 0);
$pdf->Cell($length, $cheight, get_string('total_el', $p), 1, 0, 'C', 0, '', 0);
$pdf->Ln();
//Add table content
$pdf->setFont($font, '', $tabletext);
$data = $lib->tracked_course_enrolment();
$pos = 0;
for($i = 1; $i < (count($data)+1); $i++){
    if($pos === 2){
        $pos = 0;
    }
    $pdf->setFillColor($cmykdual[$pos][0], $cmykdual[$pos][1], $cmykdual[$pos][2], $cmykdual[$pos][3]);
    $pos++;
    $pdf->Cell($length*0.5, $cheight, $i, 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length*1.5, $cheight, $data[$i-1][1], 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length, $cheight, $data[$i-1][0], 1, 0, 'C', 1, '', 0);
    $pdf->Ln();
}

//Add all learners page(s)
$pdf->addPage('L');
$pdf->setFont($font, 'B', $sixteen);
$pdf->Cell(0, 0, $or.': 2', 0, 0, 'L', 0, '', 0);
$pdf->Ln();
//Set the title
$pdf->setFont($font, 'B', $titlesize);
$pdf->Cell(0, 0, $al, 0, 0, 'C', 0, '', 0);
$pdf->setFont($font, '', $sixteen);
$pdf->Ln();
$pdf->Cell(0, $cheight, get_string('r_al_info_asc', $p), 0, 0, 'C', 0, '', 0);
$pdf->Ln();
//Set the font
$pdf->setFont($font, 'B', $sixteen);
//Add cell to page
$pdf->Cell(275, 10, $al, 1, 0, 'C', 0, '', 0);
//Add new line
$pdf->Ln();
//Add Table headers
$length = 275 / 5;
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($length / 5, $cheight, '#', 1, 0, 'C', 0, '', 0);
$pdf->Cell($length * 1.9, $cheight, $l, 1, 0, 'C', 0, '', 0);
$pdf->Cell($length * 1.9, $cheight, get_string('company', $p), 1, 0, 'C', 0, '', 0);
$pdf->Cell($length / 2, $cheight, $sd, 1, 0, 'C', 0, '', 0);
$pdf->Cell($length / 2, $cheight, get_string('last_a', $p), 1, 0, 'C', 0, '', 0);
$pdf->Ln();
//Add table content
$pdf->setFont($font, '', $tabletext);
$data = $lib->get_all_tracked_learners();
$pos = 0;
for($i = 1; $i < (count($data)+1); $i++){
    if($pos === 2){
        $pos = 0;
    }
    $pdf->setFillColor($cmykdual[$pos][0], $cmykdual[$pos][1], $cmykdual[$pos][2], $cmykdual[$pos][3]);
    $pos++;
    $pdf->Cell($length / 5, $cheight, $i, 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length * 1.9, $cheight, $data[$i-1][1], 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length * 1.9, $cheight, $data[$i-1][2], 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length / 2, $cheight, $data[$i-1][3], 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length / 2, $cheight, $data[$i-1][4], 1, 0, 'C', 1, '', 0);
    $pdf->Ln();
}

//Add never accessed users page(s)
$pdf->addPage('L');
$pdf->setFont($font, 'B', $sixteen);
$pdf->Cell(0, 0, $or.': 3', 0, 0, 'L', 0, '', 0);
$pdf->Ln();
$pdf->setFont($font, 'B', $titlesize);
$pdf->Cell(0, 0, $nau, 0, 0, 'C', 0, '', 0);
$pdf->setFont($font, '', $sixteen);
$pdf->Ln();
$pdf->Cell(0, $cheight, get_string('r_nau_info_asc', $p), 0, 0, 'C', 0, '', 0);
$pdf->Ln();
//Set the font
$pdf->setFont($font, 'B', $sixteen);
//Add cell to page
$pdf->Cell(275, 10, $nau, 1, 0, 'C', 0, '', 0);
//Add new line
$pdf->Ln();
//Add table headers
$id = 10;
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($id, $cheight, '#', 1, 0, 'C', 0, '', 0);
$length = 265 / 2;
$pdf->Cell($length, $cheight, get_string('fullname', $p), 1, 0, 'C', 0, '', 0);
$pdf->Cell($length, $cheight, get_string('account_cd', $p), 1, 0, 'C', 0, '', 0);
$pdf->Ln();
//Add table content
$pdf->setFont($font, '', $tabletext);
$data = $lib->get_all_innactive_users();
$pos = 0;
for($i = 1; $i < (count($data)+1); $i++){
    if($pos === 2){
        $pos = 0;
    }
    $pdf->setFillColor($cmykdual[$pos][0], $cmykdual[$pos][1], $cmykdual[$pos][2], $cmykdual[$pos][3]);
    $pos++;
    $pdf->Cell($id, $cheight, $i, 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length, $cheight, $data[$i-1][1], 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length, $cheight, $data[$i-1][2], 1, 0, 'C', 1, '', 0);
    $pdf->Ln();
}

//Add enrolments for the week page(s)
$pdf->addPage('L');
$pdf->setFont($font, 'B', $sixteen);
$pdf->Cell(0, 0, $or.': 4', 0, 0, 'L', 0, '', 0);
$pdf->Ln();
$pdf->setFont($font, 'B', $titlesize);
$pdf->Cell(0, 0, $ef, 0, 0, 'C', 0, '', 0);
$pdf->setFont($font, '', $sixteen);
$pdf->Ln();
$pdf->Cell(0, $cheight, get_string('r_pet_info_asc', $p), 0, 0, 'C', 0, '', 0);
$pdf->Ln();
//Set the font
$pdf->setFont($font, 'B', $sixteen);
//Add cell to page
$pdf->Cell(275, 10, $ef, 1, 0, 'C', 0, '', 0);
//Add new line
$pdf->Ln();
//Add table headers
$pdf->setFont($font, 'B', $tablehead);
$pdf->Cell($id, $cheight, '#', 1, 0, 'C', 0, '', 0);
$length = 265/3;
$pdf->Cell($length, $cheight, $c, 1, 0, 'C', 0, '', 0);
$pdf->Cell($length, $cheight, $l, 1, 0, 'C', 0, '', 0);
$pdf->Cell($length, $cheight, $sd, 1, 0, 'C', 0, '', 0);
$pdf->Ln();
//Add table content
$pdf->setFont($font, '', $tabletext);
$data = $lib->get_learner_enrolment_history(strtotime('- 8 days'), date('U'));
$pos = 0;
for($i = 1; $i < (count($data)+1); $i++){
    if($pos === 2){
        $pos = 0;
    }
    $pdf->setFillColor($cmykdual[$pos][0], $cmykdual[$pos][1], $cmykdual[$pos][2], $cmykdual[$pos][3]);
    $pos++;
    $pdf->Cell($id, $cheight, $i, 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length, $cheight, $data[$i-1][1], 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length, $cheight, $data[$i-1][3], 1, 0, 'C', 1, '', 0);
    $pdf->Cell($length, $cheight, $data[$i-1][4], 1, 0, 'C', 1, '', 0);
    $pdf->Ln();
}

//Output pdf
$pdf->Output('E-PortfolioWeeklyReport.pdf');
\local_courseanalytics\event\viewed_pdf_report::create(array('context' => \context_system::instance()))->trigger();