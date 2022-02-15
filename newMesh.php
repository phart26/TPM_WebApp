<?php
    $numMesh = sizeof($meshes);
    $from = 0;
    $to;
    if($numMesh < 20){
        $to = $numMesh;
    }else{
        $to = 20;
    }
    $numPages = ceil($numMesh/$to);
    

    while($numPages > 0){
?>
<div id="pagebreak" class="pagebreak">
    
        <div class="titleImg">
                <h2 class="title">
                    <strong>New Mesh Creation Sheet</strong>
                </h2>
        </div>
                
               
        
        
        <table class="header">
            <tr>
                <td style="width : 22%">PO#: <?= $po ?></td>
                <td style="width : 22%">Total Order: <?= $meshJob['quantity'] ?></td>
                <td style="width : 22%">Mesh Type: <?= $meshJob['mat_type'] ?></td>
            </tr>
        
            
        </table>
       
        <table class="summary-table">
            <thead>
                <tr>
                    <td>
                        <strong>Mesh No</strong>
                    </td>
                    <td>
                        <strong>Length</strong>
                    </td>
                </tr>
            </thead>
            
            <tbody>
                <?php for($i = $from ; $i < $to; $i++): ?>
                <tr>
                    <td><?= $meshes[$i]['mesh_no'] ?></td>
                    <td><?= $meshes[$i]['length'] ?></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        
        <p class="pad-b-20">&nbsp;</p>

        <style>
            *{
                padding : 0;
            }
            
            @page 
            {
                margin : 10px 60px 100px 60px
            }
            
            body
            {
                font-size: 12px;
                font-family: 'Helvetica'
            }
            
            footer 
            { 
                position: fixed; 
                bottom: -80px; 
                left: 0px; 
                right: 0px;
                height: 80px;
                width: 100%;
                font-size: 11px;
                color : #9E9E9E;
            }
            
            table
            {
                width : 100%;
            }
            
            .margin-l-5{ margin-left: 5px }
            .margin-l-10{ margin-left: 10px }
            .margin-l-15{ margin-left: 15px }
            .margin-l-20{ margin-left: 20px }
            
            .margin-b-5{ margin-bottom: 5px }
            .margin-b-10{ margin-bottom: 10px }
            .margin-b-15{ margin-bottom: 15px }
            .margin-b-20{ margin-bottom: 20px }
            
            .pad-10{padding : 10px;}
            .pad-5{padding : 5px;}
            
            .pad-l-5{ padding-left: 5px }
            .pad-l-10{ padding-left: 10px }
            .pad-l-15{ padding-left: 15px }
            .pad-l-20{ padding-left: 20px }
            
            .pad-b-5{ padding-bottom: 5px }
            .pad-b-10{ padding-bottom: 10px }
            .pad-b-15{ padding-bottom: 15px }
            .pad-b-20{ padding-bottom: 20px }

            .pad-t-5{ padding-top: 5px }
            .pad-t-10{ padding-top: 10px }
            .pad-t-15{ padding-top: 15px }
            .pad-t-20{ padding-top: 20px }
            
            
            .border {border : 1px solid #000}
            
            .box
            {
                border : 1px solid #000;
                padding : 3px 0 3px 10px;                
            }
            .summary-table 
            {
                border-collapse: collapse;
            }
            
            .summary-table td
            {
                padding-left : 5px;
            }
            
            .summary-table tbody td 
            {
                border-top: 1px solid #000;
                border-bottom: 1px solid #000;
            }
            
            .summary-table tbody tr td:first-child
            {
                border-left: 1px solid #000;
            }
            
            .summary-table tbody tr td:last-child
            {
                border-right: 1px solid #000;
            }
            .pagebreak{
            	page-break-before: always;
            }

            .pagebreak:first-child{
            	page-break-before: avoid;
            }

            .image{
                display: inline-block !important;
                margin-left: 20%; 
                vertical-align: top;
                margin-bottom: 20px;
            }

            .title{
                display: inline-block !important;
            }
            img{
                width: 250px;
                height:75px; 
                 
            }
        </style>
</div>
<?php 
   $numPages--;
   $from = $to;
   $to += 20;
    }
?>