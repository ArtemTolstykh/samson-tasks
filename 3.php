<?php
namespace Test3;

use InvalidArgumentException;

class NewBase //Указал имя класса с большой буквы
{
    // Перенес все свойства в начало класса и явно указал их типы данных
    static private int $count = 0;

    static private array $arSetName = [];

    private string $name;

    protected mixed $value;

    /**
     * @param string $name
     */
    public function __construct(string $name = '') //Явно обозначил параметр $name как string
    {
        if (empty($name)) {
            while (in_array((string)self::$count, self::$arSetName, true)) { //изменил функцию array_search на in_array так как необходимо проверить наличие элемента в массиве, а не узнать его индекс
                ++self::$count;
            }
            $name = (string)self::$count; // Сохраняю строковое значение $count
        }
        $this->name = $name;
        self::$arSetName[] = $this->name;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name; // Убрал символы "*"
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue(mixed $value): void //Для каждого метода класса принимающего параметры, явно указал тип
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getSize(): string //Теперь этот метод возвращает только длину serialize значения
    {
        return strlen(serialize($this->value));
    }

    /**
     * @return string[]
     */
    public function __sleep(): array
    {
        return ['value', 'name']; //так же возвращает и 'name'
    }

    /**
     * @return string
     */
    public function getSave(): string
    {
        $value = serialize($this->value);
        return $this->name . ':' . strlen($value) . ':' . $value;
    }

    /**
     * Этот метод отвечает за десериализацию строки
     *
     * @param string $value
     * @return NewBase
     */
    public static function load(string $value): NewBase
    {
        $arValue = explode(':', $value, 3); //ограничил explode

        $offset = strlen($arValue[0]) + 1 + strlen($arValue[1]) + 1; // Вычисление позиции в строке
        $serialized = substr($value, $offset);
        $obj = new NewBase($arValue[0]);
        $obj->setValue(unserialize($serialized, ['allowed_classes' => true])); // десериализация строки, будут загружены все классы, которые могут быть в сериализованной строке ['allowed_classes' => true]
        return $obj;
    }
}

class NewView extends NewBase
{
    private string|null $type = null;

    private int $size = 0;

    private mixed $property = null;

    /**
     * @param mixed $value
     * @return void
     */
    public function setValue(mixed $value): void
    {
        parent::setValue($value);
        $this->setType();
        $this->setSize();
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setProperty(mixed $value): static
    {
        $this->property = $value;
        return $this;
    }

    /**
     * @return void
     */
    private function setType(): void
    {
        $this->type = detectedType($this->value); //Изменил имя функции
    }

    /**
     * Этот метод выполняет расчет и установку размера свойства объекта
     *
     * @return void
     */
    private function setSize(): void
    {
        $parentSize = parent::getSize(); // Получение размера родительского объекта

        if ($this->value instanceof self) { //Если объект текущего класса
            $this->size = (int)$parentSize + 1 + strlen((string)$this->property);
        } elseif ($this->type === 'test') { // Если объект класса NewBase или его наследников (Работа функции detectedType, ранее она называлась gettype)
            $this->size = (int)$parentSize;
        } elseif (is_string($this->value)) { // Если value - это строка
            $this->size = strlen($this->value);
        } else { // В иных случаях
            $this->size = strlen(serialize($this->value));
        }
    }

    /**
     * @return string[]
     */
    public function __sleep(): array
    {
        return ['property'];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        $parentName = parent::getName();

        if (empty($parentName)) {
            throw new InvalidArgumentException("The object doesn't have name"); //Изменил Exception на InvalidArgumentException
        }
        return '"' . $parentName  . '": ';
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return ' type ' . $this->type  . ';';
    }

    /**
     * @return string
     */
    public function getSize(): string
    {
        return ' size ' . $this->size . ';';
    }

    /**
     * @return void
     */
    public function getInfo(): void
    {
        try {
            echo $this->getName() . $this->getType() . $this->getSize();
        } catch (InvalidArgumentException $exc) {
            echo 'Error: ' . $exc->getMessage();
        }
    }

    /**
     * @return string
     */
    public function getSave(): string // Убрал условную проверку для типа 'test'
    {
        $value = serialize($this->value);
        $name = parent::getName();
        $length = strlen($value);

        return $name . ':' . $length . ':' . $value . serialize($this->property);
    }

    /**
     * Этот метод выполняет десериализацию объекта из строки
     *
     * @param string $value
     * @return self
     */
    public static function load(string $value): self
    {
        $arValue = explode(':', $value, 3);

        $name = $arValue[0];
        $lenSerialized = (int)$arValue[1];

        $startSerialized = strlen($name) + 1 + strlen($arValue[1]) + 1;
        $serializedValue = substr($value, $startSerialized, $lenSerialized);
        $serializedProperty = substr($value, $startSerialized + $lenSerialized);

        $obj = new NewView($name);
        $obj->setValue(unserialize($serializedValue, ['allowed_classes' => true]));
        $obj->setProperty(unserialize($serializedProperty, ['allowed_classes' => true]));

        return $obj;
    }
}

/**
 * @param mixed $value
 * @return string
 */
function detectedType(mixed $value): string //Упростил функцию, вместо цикличного обхода всей иерархии классов, использую instanceof
{
    if ($value instanceof NewBase) {
        return 'test';
    }

    return gettype($value);
}


$obj = new NewBase('12345');
$obj->setValue('text');

$obj2 = new NewView('O9876');
$obj2->setValue($obj);
$obj2->setProperty('field');
$obj2->getInfo();

$save = $obj2->getSave();

$obj3 = NewView::load($save);

var_dump($obj2->getSave() === $obj3->getSave());


echo '<br>'.'<br>'.'value строка, property число'.'<br>';

$obj1 = new NewView('obj1');
$obj1->setValue('hello world');
$obj1->setProperty(42);
$obj1->getInfo();

$serialized = $obj1->getSave();
echo "Serialized: $serialized".'<br>';

$obj1_loaded = NewView::load($serialized);
$obj1_loaded->getInfo();

echo ' Результат совпадает: ';
var_dump($obj1->getSave() === $obj1_loaded->getSave());


echo '<br>'.'<br>'.'value объект NewBase, property строка'.'<br>';

$base = new NewBase('baseObj');
$base->setValue('inner value');

$obj2 = new NewView('obj2');
$obj2->setValue($base);
$obj2->setProperty('custom prop');
$obj2->getInfo();

$serialized2 = $obj2->getSave();
echo '<br>'."Serialized: $serialized2".'<br>';

$obj2_loaded = NewView::load($serialized2);
$obj2_loaded->getInfo();

echo '<br>'.'Результат совпадает: ';
var_dump($obj2->getSave() === $obj2_loaded->getSave());
