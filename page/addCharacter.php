<div class="contentWrapper">
    <div class="contentContainer">

        <table>
            <tr>
                <td colspan="2">
                    <h2>Personal</h2>
                </td>
            </tr>
            <tr>
                <td>Owner</td>
                <td>
                    <select>
                        <option>A</option>
                        <option>B</option>
                        <option>Shared</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Name</td>
                <td><input type="text" /></td>
            </tr>
            <tr>
                <td>Gender</td>
                <td>
                    <input type="radio" name="gender" /> Female
                    <input type="radio" name="gender" /> Male
                    <input type="radio" name="gender" /> Other
                </td>
            </tr>
            <tr>
                <td>Species</td>
                <td>
                    <input type="text" list="speciesList"/>
                    <datalist id="speciesList">
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                    </datalist>
                </td>
            </tr>
            <tr>
                <td>Birthdate</td>
                <td><input type="date" /></td>
            </tr>
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


        <button>Save</button>
    </div>
</div> 