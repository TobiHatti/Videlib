<?php 
require("../lib/connect.php");
require("../lib/wrapsql.php");
$sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));

$sql->Open();
?>

<div class="contentWrapper">
    <div class="contentContainer">
        <form id="addCharacterForm" method="post" enctype="multipart/form-data">
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
                    <td>Mother</td>
                    <td><input type="text" list="characterList" /></td>
                </tr>
                <tr>
                    <td>Father</td>
                    <td><input type="text" list="characterList" /></td>
                </tr>
            </table>
            <button type="submit">Save</button>
        </form>
    </div>
</div> 

<?php
$sql->Close();
?>