<?php

/* ****** Задание 1 ****** */

function reverseStr(string $str): string
{
    return implode('', array_reverse(mb_str_split($str)));
}

function convertString (string $a, string $b): string
{
    $counter = 0;

    $result = preg_replace_callback(
        '/' . preg_quote($b, '/') . '/u',
        function ($matches) use (&$counter)
        {
            $counter++;
            return $counter === 2 ? reverseStr($matches[0]) : $matches[0];
        },
        $a,
        2
    );

    if ($counter < 2 ) {
        throw new  InvalidArgumentException('В заданной строке содержится менее двух заданных подстрок');
    }

    return $result;
}

$stringValue = '(замок был закрыт на (замок), да замок был хлипкий';
$b = '(зам';

echo 'Результат первого задания:'.'<br>';
echo "Исходная строка: $stringValue".'<br>';
try {
    echo 'Результат: ' . convertString($stringValue, $b);
} catch (InvalidArgumentException $e) {
    echo $e->getMessage();
}

echo '<br><br>';


/* ****** Задание 2 ****** */

function mySortForKey(array $a, string $b): array
{
    $result = [];
    $countIndex = 0;
    foreach ($a as $item) {
        if (isset($item[$b])) {
            $result[] = $item[$b];
            $countIndex++;
        } else {
            throw new InvalidArgumentException('В элементе с индексом: ' . $countIndex . ' отсутствует ключ ' . $b);
        }

    }

    sort($result);

    return $result;
}

echo 'Результат второго задания'.'<br>';
$arrTask3 = [['a'=>5,'v'=>5],['v'=>3,'b'=>3],['a'=>1,'v'=>2]];

try {
    $resultTask2 = mySortForKey($arrTask3,'v');
    echo implode(', ', $resultTask2);
} catch (Exception $e) {
    echo $e->getMessage();
}


/* ****** Задание 3 ****** */
//Реализовать функцию importXml(string $filename): void
// $filename – путь к xml файлу (структура файла приведена ниже).
// Результат ее выполнения: прочитать файл $filename и импортировать его в созданную БД.
function importXml(string $filename): void
{
    $xml = simplexml_load_file($filename);

    foreach ($xml->Товар as $товар) {
        echo 'Код: ' . (string)$товар['Код'] .  '<br>';
        echo 'Название: ' . (string)$товар['Название'] .  '<br>';
    }
}

importXml('catalog.xml');










