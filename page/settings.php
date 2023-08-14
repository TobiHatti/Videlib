<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
require("../lib/util.php");
require("../lib/ageCalc.php");

AgeCalc::Init($_SESSION["VidePID"]);
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"),false);
?>


<div class="contentWrapper">
    <div class="contentContainer">
    <div class="backBtn" d-page="menu" d-pk="" d-pv=""><i class="fa-solid fa-chevron-left"></i> Back</div>

        <h1>Settings</h1>

        <details class="settingSection" open>
            <summary>General</summary>
            
            <div class="settingOption">
                <span>Party Name</span>
                <input type="text" value="" />
            </div>
            
        </details>

        <details class="settingSection" open>
            <summary>Ages</summary>
            <p>
                Define how fast Characters age. Leaving this section blank will result them to age in real time.<br>
                Multiple steps can be defined to let characters age at different rates.<br>
                Specify the days per year up to a given year.<br>
                (e.g.: Age 18, Days per year 12 = the character will age 1 year every 12 days until the age of 18)
            </p>
            
            <form method="post">
                <div class="ageSection">
                    <button type="button" style="margin: 10px auto; display: block;" id="btnAddAgeSection">Add</button>
                    <?php $i = 0; $sql->Open(); foreach($sql->ExecuteQuery("SELECT * FROM age_sections WHERE PartyID = ? ORDER BY Age ASC", $_SESSION["VidePID"]) as $age): ?>
                        <div class="ageEntry">
                            <input type="hidden" value="<?= $age["ID"]?>" name="AID<?= $i ?>" />
                            <button type="button" class="delBtn"><i class="fa-regular fa-trash-can"></i></button>
                            <div class="labeledInput">
                                <span>Label</span>
                                <input placeholder="Label..." type="text" value="<?= $age["SectionName"]?>" name="Label<?= $i ?>" required />
                            </div>
                            <div class="subContainer">
                                <div class="labeledInput">    
                                <span>Age</span>
                                <input placeholder="Age..." type="number" value="<?= $age["Age"]?>" name="Age<?= $i ?>" required /> 
                                </div>
                                <div class="labeledInput">
                                <span>Days per Year</span>
                                <input placeholder="Days..." type="number" value="<?= $age["DaysPerYear"]?>" name="Days<?= $i ?>" required />
                                </div>
                            </div>
                        </div>
                    <?php $i++; endforeach; $sql->Close(); ?>
                </div>
                <button type="submit" style="margin: 10px auto; display: block;">Save</button>                    
            </form>
        
            <template id="ageTemplate">
                <div class="ageEntry">
                    <input type="hidden" class="ageID" value="" name="" />
                    <button type="button" class="delBtn"><i class="fa-regular fa-trash-can"></i></button>
                    <div class="labeledInput">
                        <span>Label</span>
                        <input placeholder="Label..." class="ageLabel" type="text" value="" name="" required />
                    </div>
                    <div class="subContainer">
                        <div class="labeledInput">    
                        <span>Age</span>
                        <input placeholder="Age..."  class="ageVal" type="number" value="" name="" required /> 
                        </div>
                        <div class="labeledInput">
                        <span>Days per Year</span>
                        <input placeholder="Days..."  class="ageDays" type="number" value="" name="" required />
                        </div>
                    </div>
                </div>
            </template>
        </details>

        <details class="settingSection" open>
            <summary>Collaborators</summary>
            
            
        </details>

        <details class="settingSection" open>
            <summary>Share</summary>
            
            
        </details>
           
        
    </div>
</div> 