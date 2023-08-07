<?php

class AgeCalc{

    public static array $ages;
    public static function Init($partyID){
        AgeCalc::$ages = array();
        $lastAge = 0;
        $lastAccumulatedDays = 0;
        $sql = new WrapMySQL(getenv("dbHost"), getenv("dbName"), getenv("dbUser"), getenv("dbPass"));
        $sql->Open();
        foreach($sql->ExecuteQuery("SELECT * FROM age_sections WHERE PartyID = ? ORDER BY Age ASC", $partyID) as $ageEntry){

            $age = $ageEntry["Age"];
            $daysPerYear = $ageEntry["DaysPerYear"];
            $accumulated = ($age - $lastAge) * $daysPerYear + $lastAccumulatedDays;


            array_push(AgeCalc::$ages,new AgeEntry($age, $daysPerYear, $accumulated, $ageEntry["SectionName"]));

            $lastAge = $age;
            $lastAccumulatedDays = $accumulated;
        }
        $sql->Close();
    }

    public static function GetFromDate(string $birthdate, float $ageMultiplier = 1, $ageOffset = 0)
    {
        if($birthdate == "0000-00-00") return "?";

        $earlier = new DateTime($birthdate);
        $later = new DateTime(date("Y-m-d"));
        return AgeCalc::GetFromDays($later->diff($earlier)->format("%a"), $ageMultiplier, $ageOffset);
    }

    public static function GetFromDays(int $days, int $ageMultiplier = 1, $ageOffset = 0)
    {
        $days *= $ageMultiplier;
        $days += $ageOffset;
        $calculatedAge = 0;
        $lastAccumulation = 0;
        foreach(AgeCalc::$ages as $ageSection)
        {
            if($days >= $ageSection->daysAtGivenAge) $calculatedAge = $ageSection->age;
            else{
                $days -= $lastAccumulation;
                while($days >= $ageSection->daysPerYear){
                    $calculatedAge++;
                    $days-=$ageSection->daysPerYear;
                }
                return $calculatedAge;
            }
            $lastAccumulation = $ageSection->daysAtGivenAge;
        }
        return -1;
    }

    public static function GetDescriptor(string $birthdate, float $ageMultiplier = 1, $ageOffset = 0){
        $age = AgeCalc::GetFromDate($birthdate, $ageMultiplier, $ageOffset);
        $lastEntry = "";
        foreach(AgeCalc::$ages as $ageEntry){
            if($ageEntry->age > $age) return $ageEntry->name;

        }
    }

}

class AgeEntry{
    public int $age;
    public int $daysPerYear;
    public int $daysAtGivenAge;
    public string $name;

    public function __construct(string $age, string $daysPerYear, string $daysAtGivenAge, string $name)
    {
        $this->age = $age;
        $this->daysPerYear = $daysPerYear;
        $this->daysAtGivenAge = $daysAtGivenAge;
        $this->name = $name;
    }
}

?>