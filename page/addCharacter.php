<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$sql->Open();

class Character{
    public int $value = 0;
    public string $name = "";

    public function __construct(int $value, string $name)
    {
        $this->value = $value;
        $this->name = $name;
    }
}

$characters = array();
foreach($sql->ExecuteQuery("SELECT * FROM characters INNER JOIN users ON characters.COwnerID = users.ID ORDER BY Name ASC") as $row)
{
    array_push($characters, new Character($row["ID"], $row["Name"]." (".$row["Symbol"].") [".$row["Species"]."]"));
}

?>

<div class="contentWrapper">
    <div class="contentContainer">
        <form id="addCharacterForm" method="post" enctype="multipart/form-data" autocomplete="off">
            <table>
                <tr>
                    <td colspan="3">
                        <h2>Personal</h2>
                    </td>
                </tr>
                <tr>
                    <td rowspan="5">
                        
                        <label for="imgBtn" class="imgUploadBtn">
                            <img src="#" id="imgPreview"/>
                        </label>
                        <input type="file" name="image" id="imgBtn" accept="image/*" hidden/>
                    </td>
                    <td>Owner</td>
                    <td>
                        <select name="owner">
                        <?php foreach($sql->ExecuteQuery("SELECT * FROM users") as $row): ?>
                            <option value="<?= $row["ID"] ?>"><?= $row["Username"] ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Name</td>
                    <td><input type="text" name="name" required/></td>
                </tr>
                <tr>
                    <td>Gender</td>
                    <td>
                        <input type="radio" value="F" name="gender" required/> Female
                        <input type="radio" value="M" name="gender" required/> Male
                        <input type="radio" value="X" name="gender" required/> Other
                    </td>
                </tr>
                <tr>
                    <td>Species</td>
                    <td>
                        <input type="text" list="speciesList" name="species"/>
                        <datalist id="speciesList">
                            <?php foreach($sql->ExecuteQuery("SELECT DISTINCT Species FROM characters") as $row): ?>
                                <option value="<?= $row["Species"] ?>"><?= $row["Species"] ?></option>
                            <?php endforeach; ?>

                        </datalist>
                    </td>
                </tr>
                <tr>
                    <td>Birthdate</td>
                    <td><input type="date" name="birthdate"/></td>
                </tr>
            </table>
            <table>
                <tr>
                    <td colspan="2">
                        <h2>Relations</h2>
                    </td>
                </tr>
                <tr>
                    <td>Mother (Biological)</td>
                    <td>
                        <select name="biomother">
                            <option value="" selected>---</option>
                            <?php foreach($characters as $row): ?>
                                <option value="<?= $row->value ?>"><?= $row->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Father (Biological)</td>
                    <td>
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
                    <td>Mother (Adopted)</td>
                    <td>
                        <select name="adoptmother">
                            <option value="" selected>---</option>
                            <?php foreach($characters as $row): ?>
                                <option value="<?= $row->value ?>"><?= $row->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>Father (Adopted)</td>
                    <td>
                        <select name="adoptfather">
                            <option value="" selected>---</option>
                            <?php foreach($characters as $row): ?>
                                <option value="<?= $row->value ?>"><?= $row->name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
   

            </table>
            <button type="submit">Save</button>
        </form>
    </div>
</div> 

<?php
$sql->Close();
?>