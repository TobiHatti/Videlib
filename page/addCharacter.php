<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$sql->Open();

class Character{ 
    public string $value = "";
    public string $name = "";

    public function __construct(string $value, string $name)
    {
        $this->value = $value;
        $this->name = $name;
    }
}

$characters = array();
foreach($sql->ExecuteQuery("SELECT *, characters.ID AS CID FROM characters INNER JOIN users ON characters.COwnerID = users.ID WHERE characters.PartyID = ? ORDER BY Name ASC", $_SESSION["VidePID"]) as $row)
    array_push($characters, new Character($row["CID"], $row["Name"]." (".$row["Symbol"].") [".$row["Species"]."]"));
?>

<div class="contentWrapper">
    <div class="contentContainer">
        <form id="addCharacterForm" method="post" enctype="multipart/form-data" autocomplete="off">
            <h2>Personal</h2>
            <div class="personalSection">
                <div class="imgUploadSection">
                    <label for="imgBtn" class="imgUploadBtn">
                        <img src="files/placeholders/question.webp" id="imgPreview"/>
                    </label>
                    <input type="file" name="image" id="imgBtn" accept="image/*" hidden/>
                </div>
                <table>
                    <tr>
                        <td>
                            <span>Owner</span>
                            <select name="owner">
                            <?php foreach($sql->ExecuteQuery("SELECT * FROM users INNER JOIN party_users ON users.ID = party_users.UserID WHERE PartyID = ?",$_SESSION["VidePID"]) as $row): ?>
                                <option value="<?= $row["ID"] ?>"><?= $row["Username"] ?></option>
                            <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Name</span>
                            <input type="text" name="name" required/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Gender</span>
                            <div style="margin: 5px 0;">
                                <input type="radio" value="F" name="gender" required/> Female
                                <input type="radio" value="M" name="gender" required/> Male
                                <input type="radio" value="X" name="gender" required/> Other
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Species</span>
                            <input type="text" list="speciesList" name="species"/>
                            <datalist id="speciesList">
                                <?php foreach($sql->ExecuteQuery("SELECT DISTINCT Species FROM characters WHERE PartyID = ?", $_SESSION["VidePID"]) as $row): ?>
                                    <option value="<?= $row["Species"] ?>"><?= $row["Species"] ?></option>
                                <?php endforeach; ?>

                            </datalist>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Birthdate</span>
                            <input type="date" name="birthdate"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Multiplier</span>
                            <input type="number" min="1" value="1" name="ageMultiplier" required/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span>Offset (Days)</span>
                            <input type="number" step="1" value="0" name="ageOffset" required/>
                        </td>
                    </tr>
                </table>
            </div>
            <h2>Relations</h2>
            <table class="relationsSection">
                <tr>
                    <td>
                        <span>Mother (Biological)</span>
                        <select name="biomother">
                            <option value="" selected>---</option>
                            <?php foreach($characters as $row): ?>
                                <option value="<?= $row->value ?>"><?= $row->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>Father (Biological)</span>
                        <select name="biofather">
                            <option value="" selected>---</option>
                            <?php foreach($characters as $row): ?>
                                <option value="<?= $row->value ?>"><?= $row->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr><td><br></td></tr>
                <tr>
                    <td>
                        <span>Mother (Adopted)</span>
                        <select name="adoptmother">
                            <option value="" selected>---</option>
                            <?php foreach($characters as $row): ?>
                                <option value="<?= $row->value ?>"><?= $row->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span>Father (Adopted)</span>
                        <select name="adoptfather">
                            <option value="" selected>---</option>
                            <?php foreach($characters as $row): ?>
                                <option value="<?= $row->value ?>"><?= $row->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <button type="submit" class="addCharacterSubmitBtn">Save</button>
        </form>
    </div>
</div> 

<?php
$sql->Close();
?>