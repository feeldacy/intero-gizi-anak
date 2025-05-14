<?php
namespace App\Services;

use App\Models\NutritionRecord;

class NutritionRecordService{
    public function storeNutritionRecord($data) {
        return NutritionRecord::create($data);
    }

    public function updateNutritionRecord($nutritionRecord, $data) {
        if ($nutritionRecord) {
            $nutritionRecord->update($data);
            return $nutritionRecord;
        }
        return $nutritionRecord;
    }

    public function calculateBMI(float $weight, float $height): float {
        // Convert height from cm to meters
        $heightInMeters = $height / 100;
        // Calculate BMI
        return round($weight / ($heightInMeters * $heightInMeters), 2); // Round BMI to 2 decimal places
    }

    public function classifyBMI(float $bmi): string {
        if ($bmi < 18.5) {
            return 'Berat Badan Kurang (Underweight)';
        } elseif ($bmi >= 18.5 && $bmi < 22.9) {
            return 'Normal';
        } elseif ($bmi >= 23 && $bmi < 24.9) {
            return 'Kelebihan Berat Badan (Overweight)';
        } elseif ($bmi >= 25 && $bmi < 29.9){
            return 'Obesitas I';
        } else {
            return 'Obesitas II';
        }
    }
}

?>

