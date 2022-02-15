    <div id="pagebreak" class="pagebreak">
        
        <div>
            <img src="logo_tpm.png" style="float: left;" alt="TPM Logo" />
            <h1 style="float: right; width: 50%;text-align: right;">TUBULAR PERFORATING</h1>
            <div style="clear: both;"></div>
            <h3 style="text-align: center;">THIS IS REQUIRED AT SET-UP AND DAILY START UP.</h3>
        </div>

        <hr>
        <div style="padding-top: 0%">
            <h3>DATE : ____________________</h3>
            <h3 style="padding-top: 2%;">JOB NUMBER : <?= $job ?></h3>
            <h3 style="padding-top: 2%;">MILL NUMBER : <?= $records['mill'] == '' ? '______________________' : $records['mill']; ?></h3>
            <h3 style="padding-top: 2%;">MATERIAL THICKNESS CHECK : <?= $records['thickness'] == '' ? '______________________' : $records['thickness']; ?> Inch (<?= $header['gage'] ?> Gage )  </h3>
            <div style="float: right; width: 30%; border: 1px solid; padding: 15px">
                <ul><li>MILL OPERATORS ARE REQUIRED TO TAG EVERY COIL AFTER USE WITH PROPER IDENTIFICATION.</li>
                    <li>IF A COIL NUMBER IS NOT LISTED ON THE COIL YOU MUST KNOW THE OFFICE <span style="text-decoration: underline;">IMMEDIATLY</span>.
                    </li>
                    <li>ANY LEFT OVER COIL MUST HAVE NEW TAG WITH THE CORRECT IDENTIFICATION. BRING THE TAG, OF THE COIL THAT WAS USED, TO THE OFFICE. WE WILL GIVE THE NEW TAG TO MILL OPERATORS TO TAG THE LEFT OVER COIL.
                    </li>
                </ul>
            </div>
            <h3 >DIMPLE/SLOT DEPTH CHECK : <?= $records['depth_of_dimple'] == '' ? '______________________' : $records['depth_of_dimple']; ?>
                
                <span class="pad-l-10"><?= $header['depth_of_dimple_plus'] ?> </span>
                    <span class="pad-l-10"><?= $header['depth_of_dimple_minus'] ?></span>
            </h3>

            <h3 style="padding-top: 2%;">BLANK END RING : <?= $records['blank_end'] == '' ? '______________________' : $records['blank_end']; ?> 
            <span class="pad-l-10"><?= $header['blank_end_plus'] ?> </span>
                    <span class="pad-l-10"><?= $header['blank_end_minus'] ?></span>  </h3>

            <h3 style="padding-top: 2%;">BLANK AREA : END 1- _____________ END 2- ___________</h3>

            <h3 style="padding-top: 2%;">BEND TEST-PASS OR FAIL ______________________</h3>

            <h3 style="padding-top: 2%;">REQUIRED DRIFT SIZE : <?= $records['id_drift'] == '' ? '_______________________' : $records['id_drift'];  ?></h3>
            <h3 style="padding-top: 2%;">DRIFT PASS OR FAIL : _______________________</h3>
       
            <div style="float: left;">
                <h3>MILL OPERATOR : __________________________</h3>
                <h3 style="padding-top: 2%;">INSPECTOR : __________________________</h3>
            </div>
            <div style="float: right;">
                <h3>DATE : __________________________</h3>
                <h3 style="padding-top: 2%;">DATE : __________________________</h3>
            </div>
            <div style="clear: both;"></div>
        </div>

        <div style="padding-top: 10px;width: 100%; font-size: 16px;color: #565656f7">
            <p>*Material thicness and dimple depth(if applicable) check must be performed on material before any parts are ran on mill, daily at start-up.<br>
            **A bend test is required at each start-up.<br>
            ***Run 1st part & send to inspection department for drifting before running order. Mill operator and the inspector signature is required if pass/fail. This is required at set up and daily start-up.<br>

            ****Must be turned in daily.<br>
            *****Inspect and record blank area(if applicable).</p>
        </div>

        <!-- <div style="padding-top: 10px;">
            <p style="font-size: 13px;">TPM-FPD-001 REV NONE 06/05/2018</p>
        </div> -->
         <footer>
                <!-- <p class="margin-b-10">
                    Data represented on this report is TPM controlled information and can be modified by only authorized Information Technology personnel.
                </p> -->
                
                <p>
                    <?= $footer['first_part_drift']; ?><br>
                    <?= $footer['first_part_drift-2']; ?><br>
                    <?= $footer['first_part_drift-3']; ?><br>
                    <?= $footer['first_part_drift-4']; ?>
                    <!-- <span style="margin-left:10px;">
                      
                    </span> -->
                </p>
               
            </footer>
        
        
        
</div>
