<?php


require_once __DIR__ . '/tcpdf/tcpdf.php';

// require_once __DIR__ . '/reports_db.php';
include_once($_SERVER['DOCUMENT_ROOT'].'/api/core/config.php');
$db = new mysqli($config['database']['host'], $config['database']['username'], $config['database']['password'], $config['database']['database']);


/**
 * Description of CutOffOperator
 *
 * @author eng.mahmoud
 */
 
 
class CutOffOperator extends TCPDF {


  
public function __construct($customer_name) {
//    parent::__construct();
    
parent::__construct('L', 'pt', 'A4', true, 'UTF-8', false);
    
$this->SetCreator('Eng. Mahmoud S. S. Abuwarda');


    
global $db;

    
$lg['a_meta_charset'] = 'UTF-8';
    
$lg['a_meta_dir'] = 'ltr';
    
$lg['a_meta_language'] = 'en';
    
$lg['w_page'] = 'page';

    
$this->setLanguageArray($lg);

    
$this->AddPage();
    
$this->Ln();

    
$this->SetY(50);

    
$this->Cell(540, 13, 'Training Report - Cutoff Operator', 0, 0, 'R');

    
$this->Ln();
    
$this->Ln();
    
$this->Ln();

    
$this->Cell(120, 13, 'Employee:', 0, 0, 'L');

    
$this->Ln();

    $this->Cell(120, 13, 'Date', 0, 0, 'L');
    
$this->Cell(120, 13, 'Customer', 0, 0, 'L');
    
$this->Cell(120, 13, 'Job', 0, 0, 'L');
    
$this->Cell(120, 13, 'Vendor Po', 0, 0, 'L');
    
$this->Cell(120, 13, 'Part No.', 0, 0, 'L');

    
$this->Ln();
    $this->writeHTML('<hr />');
    
$this->Ln();
    
$this->writeHTML('<hr height="2px"/>');
			
	$sql = "SELECT 
				orders_tbl.cutoff_operator, 
				jobs.customer, 
				orders_tbl.job, 
				orders_tbl.po, 
				orders_tbl.part, 
				DATE_FORMAT(orders_tbl.began, '%m/%d/%Y') began
			FROM 
				jobs INNER JOIN 
				orders_tbl ON jobs.job = orders_tbl.job
			GROUP BY orders_tbl.cutoff_operator, jobs.customer, orders_tbl.job, orders_tbl.po, orders_tbl.part, orders_tbl.began
			HAVING (((orders_tbl.cutoff_operator) Is Not Null) AND ((jobs.customer) Like '%{$customer_name}%') AND ((orders_tbl.began)>=Now()-180))
			ORDER BY orders_tbl.cutoff_operator";
		
//    die($sql);
    
$r = $db->query($sql);
    
while ($i = $r->fetch_assoc()) {
      
$this->Cell(120, 13, $i['began'], 0, 0, 'L');
      
$this->Cell(120, 13, $i['customer'], 0, 0, 'L');
      
$this->Cell(120, 13, $i['job'], 0, 0, 'L');
      
$this->Cell(120, 13, $i['po'], 0, 0, 'L');
      
$this->Cell(120, 13, $i['part'], 0, 0, 'L');
      
$this->Ln();
    }
  }

}
