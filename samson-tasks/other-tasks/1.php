<?php

function printArr($array): void { // вспомогательная функция для вывода массива на страницу в браузере
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

// ********* Задание 1 *********

function isPrime(int $num): bool
{
    if ($num < 2) return false;

    for ($j = 2, $numSqrt = sqrt($num); $j <= $numSqrt; $j++) {
        if ($num % $j == 0) return false;
    }
    return true;
}

function findSimple(int $a, int $b): array
{
    if ($a > $b) {
        throw new InvalidArgumentException('Переменная а больше b');
    } elseif ($a === $b) {
        throw new InvalidArgumentException('Переменная a равна b');
    } elseif ($a < 0) {
        throw new InvalidArgumentException('Переменная а меньше нуля');
    }

    $simpleNums = [];

    for ($i = $a; $i <= $b; $i++) {
        if (isPrime($i)) {
            $simpleNums[] = $i;
        }
    }

    return $simpleNums;
}

try {
    $resultTask1 = findSimple(1, 78); //первое значение - начало диапазона, второе - конец
    echo 'Результат первого задания: ';
    echo implode(', ', $resultTask1);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
}

echo '<br><br>';


// ********* Задание 2 *********

function createTrapeze(array $a): array
{
    if (array_key_first($a) === null) {
        throw new InvalidArgumentException('Передан пустой массив');
    }

    $countElement = count($a);

    if (!($countElement % 3 == 0)) {
        throw new InvalidArgumentException('Кол-во элементов массива не кратно 3');
    }

    foreach ($a as $value) {
        if ($value < 0) {
            throw new InvalidArgumentException('В массиве присутствуют отрицательные значения!');
        }
    }

    $result = [];

    for ($i = 0; $i < $countElement; $i += 3) {
        $threeNumbers = ['a' => $a[$i], 'b' => $a[$i + 1], 'c' => $a[$i + 2]];
        $result[] = $threeNumbers;
    }

    return $result;
}

$arrTask2 = [1, 2, 4, 6, 7, 30, 23, 11, 2]; // значения исходного массива
$nullArr = [];

try {
    echo 'Результат второго задания: '.'<br>';
    $arrTask3 = createTrapeze($arrTask2);
    printArr($arrTask3);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
}

echo '<br><br>';

// ********* Задание 3 *********

function squareTrapeze(array &$a): void
{
    foreach ($a as &$key) {
        $key['s'] = (($key['a'] + $key['b']) * $key['c']) / 2;
    }

    unset($key);
}


try {
    echo 'Результат третьего задания:'.'<br>';
    if (isset($arrTask3)) {
        squareTrapeze($arrTask3);
        printArr($arrTask3);
    } else {
        throw new InvalidArgumentException('Недопустимое значение массива');
    }
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
}


echo '<br><br>';

// ********* Задание 4 *********

$a = [
    ['a' => 1, 'b' => 3, 'c' => 5, 's' => 10.0],
    ['a' => 4, 'b' => 6, 'c' => 8, 's' => 40.0],
    ['a' => 5, 'b' => 6, 'c' => 7, 's' => 38.5],
    ['a' => 6, 'b' => 4, 'c' => 2, 's' => 10.0],
];

function getSizeForLimit(array $a, float $b): array
{
    if ($b < 0) {
        throw new InvalidArgumentException('Переменная b < 0');
    }

    $result = [];
    $maxS = 0;

    foreach ($a as $trapeze) {
        if (!(isset($trapeze['s']))) {
            throw new InvalidArgumentException('В полученном массиве нет ключа с площадью (s)');
        }

        $s = $trapeze['s'];
        if ($s <= $b && $s > $maxS) {
            $maxS = $s;
            $result = $trapeze;
        }
    }

    if (empty($result)) {
        throw new InvalidArgumentException('Не нашлось трапеции, площадь которой меньше $b');
    }

    return $result;
}

try {
    echo 'Результат четвертого задания:'.'<br>';
    if (isset($arrTask3)) {
        printArr(getSizeForLimit($arrTask3, 39));
    } else {
        throw new InvalidArgumentException('Недопустимое значение массива');
    }
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
}

echo '<br><br>';
    

// ********* Задание 5 *********

abstract class BaseMath
{
    public function exp1(float $a,  float $b, float $c): float
    {
        return $a * ($b ** $c);
    }

    public function exp2(float $a,  float $b, float $c): float
    {
        return ($a / $b) ** $c;
    }

    abstract function getValue(): float;
}

// ********* Задание 6 *********

class F1 extends BaseMath
{
    public function __construct(private float $a, private float $b, private float $c)
    { }

    public function getValue(): float
    {
        return $this->exp1($this->a, $this->b, $this->c) + (($this->exp2($this->a, $this->c, $this->b) % 3) ** min($this->a, $this->b, $this->c));
    }

}

$expMath = new F1(2.2, 3.0, 4.0);

echo 'Результат пятого задания:'.'<br>';

$resultTask5Exp1 = $expMath->exp1(2, 2, 2); // 2 * (2^2) = 8
echo "Результат метода exp1: $resultTask5Exp1".'<br>';

$resultTask5Exp2 = $expMath->exp2(4, 2, 2); // (4 / 2)^2 = 4
echo "Результат метода exp2: $resultTask5Exp2".'<br>';

echo '<br>';

echo 'Результат шестого задания:'.'<br>';
echo $expMath->getValue();

