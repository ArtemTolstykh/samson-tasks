<?php

require_once 'db_conn.php';

/* ****** Задание 1 ****** */
function reverseStr(string $str): string
{
    return implode('', array_reverse(mb_str_split($str)));
}

function reverseInRange(string $str, int $start, int $end): string
{
    $before = mb_substr($str, 0, $start);
    $range = mb_substr($str, $start, $end - $start + 1);
    $after = mb_substr($str, $end + 1);

    $reverseRange = reverseStr($range);
    return $before . $reverseRange . $after;

}

function convertString(string $a, string $b): string
{
    $subStrLen = mb_strlen($b);

    $offset = 0;
    $positions = [];
    $countPos = 0;

    while (($pos = mb_strpos($a, $b, $offset)) !== false) {
        $positions[] = $pos;
        $offset = $pos + $subStrLen;
        $countPos++;

        if ($countPos == 2) {
            break;
        }
    }

    if ($countPos < 2) {
        return $a;
    }

    $start = $positions[1];
    $end = $start + $subStrLen - 1;

    return reverseInRange($a, $start, $end);
}

$stringValue = 'OneTwoThreeOneOne';
$substr = 'One';

echo 'Результат 1 задания:'.'<br>';
echo convertString($stringValue, $substr);

echo '<br><br>';

/* ****** Задание 2 ****** */

function printArr($array): void {
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

function mySortForKey(array $a, string $b): array
{
    $countIndex = 0;

    foreach ($a as $item) {
        if (!(isset($item[$b]))) {
            throw new InvalidArgumentException('В элементе с индексом: ' . $countIndex . ' отсутствует ключ ' . $b);
        }
        $countIndex++;
    }

    usort($a,
        function($firstItem, $secondItem) use ($b)
        {
            return $firstItem[$b] <=> $secondItem[$b];
        }
    );

    return $a;
}

$arrTask2 = [
    ['a'=>1,'b'=>5,'c'=>9],
    ['a'=>5,'b'=>5,'c'=>2],
    ['a'=>3,'b'=>4,'c'=>5]
];

echo 'Результат второго задания'.'<br>';

try {
    $resultTask2 = mySortForKey($arrTask2,'c');
    printArr($resultTask2);
} catch (Exception $e) {
    echo $e->getMessage();
}

echo '<br><br>';


/* ****** Задание 3 ****** */

function transliterate(string $str): string
{
    $library = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'ju', 'я' => 'ja'
    ];

    $result = strtr(mb_strtolower($str), $library);
    return ucfirst($result);
}

function importXml(string $filename): void
{
    global $conn;
    $dom = new DOMDocument();
    $dom->load($filename);

    $products = $dom->getElementsByTagName('Товар');

    foreach ($products as $product) {
        $productCode = (int) $product->getAttribute('Код');
        $productName = $product->getAttribute('Название');

        $checkUniq = $conn->query("SELECT id FROM a_product WHERE code = '$productCode'");

        if ($checkUniq->fetch_column()) {
            echo "Продукт под названием $productName с кодом $productCode уже существует в базе данных".'<br>';
            continue;
        }

        $stmt = $conn->prepare("INSERT INTO a_product (code, name) VALUES (?, ?)");
        $stmt->bind_param('is', $productCode, $productName);
        $stmt->execute();
        $productId = $conn->insert_id;

        $prices = $product->getElementsByTagName('Цена');
        foreach ($prices as $price) {
            $priceType = $price->getAttribute('Тип');
            $priceValue = $price->nodeValue;
            $stmt = $conn->prepare("INSERT INTO a_price (product_id, price_type, price) VALUES (?, ?, ?)");
            $stmt->bind_param('isd', $productId, $priceType, $priceValue);
            $stmt->execute();
        }

        $properties = $product->getElementsByTagName('Свойства')->item(0)->childNodes;
        foreach ($properties as $property) {
            if ($property instanceof DOMElement) {
                $propertyName = $property->nodeName;
                $propertyValue = $property->nodeValue;
                $unitMeasurement = $property->hasAttribute('ЕдИзм') ? $property->getAttribute('ЕдИзм') : null;
                $stmt = $conn->prepare("INSERT INTO a_property (product_id, property_name, property_value, unit_measurement) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('isss', $productId, $propertyName, $propertyValue, $unitMeasurement);
                $stmt->execute();
            }
        }

        $categories = $product->getElementsByTagName('Разделы')->item(0)->childNodes;
        $parentId = null;
        foreach ($categories as $category) {
            if ($category instanceof DOMElement) {
                $categoryName = $category->nodeValue;
                $transliterateCategory = transliterate($categoryName);

                $stmt = $conn->prepare("SELECT id FROM a_category WHERE name = ? AND parent_id <=> ? LIMIT 1");
                $stmt->bind_param('si', $categoryName, $parentId);
                $stmt->execute();

                $id = $stmt->get_result()->fetch_column();

                if (!$id) {
                    $stmt = $conn->prepare("INSERT INTO a_category (name, parent_id, code) VALUES (?, ?, ?)");
                    $stmt->bind_param('sis', $categoryName, $parentId, $transliterateCategory);
                    $stmt->execute();
                    $id = $conn->insert_id;
                }
                $parentId = (int)$id;
            }
        }

        $stmt = $conn->prepare("INSERT INTO a_product_category (product_id, category_id) VALUES (?, ?)");
        $stmt->bind_param('ii', $productId, $parentId);
        $stmt->execute();
    }
}

//importXml('catalog.xml');


/* ****** Задание 4 ****** */

function getCategories(): array
{
    global $conn;

    $rows = [];
    $result = $conn->query("SELECT * FROM a_category");
    while ($row = $result->fetch_assoc()) $rows[] = $row;
    return $rows;
}

function collectSubtreeIds(array $categories, int $rootId): array
{
    $branch = [$rootId];
    $result = [$rootId];

    while ($branch) {
        $parent = array_pop($branch);
        foreach ($categories as $category) {
            if ($category['parent_id'] == $parent) {
                $result[] = $category['id'];
                $branch[] = $category['id'];
            }
        }
    }
    return $result;
}

function findCategoryIdByName(array $categories, string $name): ?int
{
    foreach ($categories as $category) {
        if(strcasecmp($category['name'], $name) === 0 || strcasecmp($category['code'], $name) === 0) { // fix
            return (int)$category['id'];
        }
    }

    return null;
}

function exportXml(string $filename, string $categoryCode): void
{
    global $conn;

    $categories = getCategories();

    if (ctype_digit($categoryCode)) {
        $rootId = (int)$categoryCode;
    } else {
        $rootId = findCategoryIdByName($categories, $categoryCode);
        if ($rootId === null) {
            throw new InvalidArgumentException("Категория: '$categoryCode' не найдена");
        }
    }

    $ids = collectSubtreeIds($categories, $rootId);
    $branch = implode(',', $ids);

    $categoryMap = [];
    foreach ($categories as $category) {
        $categoryMap[$category['id']] = $category;
    }

    $orderedAncestorIds = [];
    foreach ($ids as $categoryId) {
        $patch = [];
        $currentId = $categoryId;

        while (isset($categoryMap[$currentId])) {
            array_unshift($patch, $currentId);
            $parentId = $categoryMap[$currentId]['parent_id'];
            if ($parentId === null || $parentId == 0) break;
            $currentId = $parentId;
        }

        $patch = array_reverse($patch);

        foreach ($patch as $id) {
            $orderedAncestorIds[] = $id;
        }

    }

    $orderedAncestorIds = array_values(array_unique($orderedAncestorIds));
    $ancestorsList = empty($orderedAncestorIds) ? '0' : implode(',', $orderedAncestorIds);

    $stmt = $conn->prepare(
        "SELECT p.id, p.code, p.name,
            GROUP_CONCAT(DISTINCT CONCAT(prop.property_name, ': ', prop.property_value, IF(prop.unit_measurement IS NOT NULL, CONCAT(' ', prop.unit_measurement), '')) SEPARATOR ', ') AS props,
            GROUP_CONCAT(DISTINCT CONCAT(pr.price_type, ': ', pr.price) SEPARATOR ', ') AS prices,
            (
                SELECT GROUP_CONCAT(DISTINCT name ORDER BY FIELD(id, $ancestorsList) SEPARATOR ', ')
                FROM a_category
                WHERE id IN ($ancestorsList)    
            ) AS cats
        FROM a_product p
        JOIN a_product_category pc ON pc.product_id = p.id
        LEFT JOIN a_property prop ON prop.product_id = p.id
        LEFT JOIN a_price pr ON pr.product_id = p.id
        WHERE pc.category_id IN ($branch)
        GROUP BY p.id, p.code, p.name");
    $stmt->execute();
    $rows = $stmt->get_result();

    $dom = new DOMDocument('1.0', 'utf-8');
    $dom->formatOutput = true;

    $root = $dom->createElement('Товары');
    $dom->appendChild($root);

    foreach ($rows as $row) {
        $product = $dom->createElement('Товар');
        $product->setAttribute('Код', $row['code']);
        $product->setAttribute('Название', $row['name']);

        $prices = $row['prices'] ? explode(', ', $row['prices']) : [];
        foreach ($prices as $price) {
            [$priceType, $priceValue] = array_map('trim', explode(':', $price, 2));
            $price = $dom->createElement('Цена', $priceValue);
            $price->setAttribute('Тип', $priceType);
            $product->appendChild($price);
        }

        $propertyNode = $dom->createElement('Свойства');
        $propertyParts = $row['props'] ? explode(', ', $row['props']) : [];
        foreach ($propertyParts as $propertyPart) {

            $parts = array_map('trim', explode(':', $propertyPart, 2));
            if (count($parts) < 2) continue;
            [$propertyName, $valueWithUnit] = $parts;

            $valueParts = explode(' ', $valueWithUnit, 2);
            $value = $valueParts[0];
            $unit = $valueParts[1] ?? null;

            $property = $dom->createElement($propertyName, $value);

            if ($unit !== null && $unit !== '') {
                $property->setAttribute('ЕдИзм', $unit);
            }
            $propertyNode->appendChild($property);
        }
        $product->appendChild($propertyNode);

        $categoriesNode = $dom->createElement('Разделы');
        $categoriesParts = $row['cats'] ? explode(', ', $row['cats']) : [];
        foreach ($categoriesParts as $categoryName) {
            if ($categoryName === '') continue;
            $categoriesNode->appendChild($dom->createElement('Раздел', $categoryName));
        }
        $product->appendChild($categoriesNode);

        $root->appendChild($product);
    }

    file_put_contents($filename, $dom->saveXML());
}

$catCode = 'Bumaga';

try {
    exportXml('testXMLcat1.xml', $catCode);
} catch(\Exception $e) {
    echo $e->getMessage();
}







