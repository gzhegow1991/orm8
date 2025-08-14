# ORM

Пакет на основе `illuminate/database` (Laravel Eloquent, в простонародье "ёлка"), доработанный с помощью функционала Persistence и некоторыми другими полезными функциями

```
PS. Использование `symfony/doctrine` это конечно хорошо. Но человека нужно ему учить, долго учить, потом он сам должен много учить, а потом эта штука будет работать медленнее, чем обычные запросы к PDO, поэтому лучше использовать "ёлку"
PS2. Можно использовать PDO и встроенный в PHP способ работы с базами данных. К сожалению, этот метод недостаточно прост в использовании, а также требует постоянной проверки на SQL-иньекции вручную либо подсчета биндов для запроса... Это можно (и, по-хорошему) нужно делать, но далеко не все специалисты на рынке имеют достаточно опыта, чтобы делать это без подготовки и набитых шишек. И потом, бизнес-задачи имеют свойство "наращиваться", а вот собирать из кусков SQL запрос это тот ещё ад, сильно проще использовать для этого ёлковский билдер
```

Рекомендации при работе с ORM:

- не создавать модели в коде используя `new ModelClass()`, использовать для этого `ModelClass::new()`. Это позволит работать проверкам в __get()/__set() на возможность проставления и получения данных.

```
Так, в ёлке метод __get() может выполнить запрос, если свойство является связью. Разумно включить блокировку ленивых запросов в классе модели.
Также добавлена возможность блокировки ленивого чтения, чтобы была возможность оперировать ровно теми данными, что получены из БД, без применения магических атрибутов, которые могут возвращать значение по-умоланию, если оно там было.

Так, в ёлке метод __set() может проставить свойства, которые являются ID, при клонировании существующей модели.
Также добавлена возможность блокировки ленивой записи, что позволяет работать с таблицами с историческими данными, которые не должны меняться вручную.
```

- добавлена возможность установить префикс для связей (по умолчанию символ `_`);

```
Это сильно упрощает чтение кода, разделяя связи и свойства, а также ускоряет проверку при считывании свойства на предмет "нужно ли интерпретировать свойство как связь"

Eloquent::setRelationPrefix('_');

/**
 * @property int            $id
 * @property string         $name
 *
 * @property int            $demo_foo_id
 * @property DemoFooModel   $_demoFoo
 */
abstract class AbstractModel extends \Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModel
{
}

/**
 * @property int            $id
 * @property string         $name
 *
 * @property int            $demo_foo_id
 * @property DemoFooModel   $_demoFoo
 */
class MyModel extends AbstractModel
{
    protected static function relationClasses() : array
    {
        return [
            '_demoFoo'  => BelongsTo::class,
        ];
    }

    public function _demoFoo() : BelongsTo
    {
        return $this->relation()
            ->belongsTo(
                __FUNCTION__,
                DemoFooModel::class
            )
        ;
    }
}
```

- добавлена возможность задавать колонки для выборки по-умолчанию, включая связи

```
Это сделано для того, чтобы при выборке модели из БД не тянуть все колонки, включая те, что могут хранить большие JSON данные, это можно задать в модели в свойстве $columns

class MyModel extends AbstractModel
{
    protected $columns = [ '*' ]; // достает все колонки, как по-умолчанию в Eloquent, для development окружения
    // protected $columns = [ '#' ]; // только PrimaryKey (не во всех таблицах он есть)
    // protected $columns = [ '#', 'name' ]; // только PrimaryKey и колонка `name`
}

Инструмент отлично сочетается с $preventsLazyGet/$preventsLazySet - в этом случае отстутствующие колонки будут прерывать работу программы, позволяя исправить код.
При выборке по связям используя ->with()/->load() колонки так же не будут выбираться автоматически, и да, это может привести к неверной работе связей, поэтому используйте этот инструмент с умом и вручную добавляйте нужные колонки.

$query = MyModel::query();
$query->addColumn('name');
$models = $query->get();
```

- для пагинации и выборки большого числа записей пользуйтесь инструментом выборки по чанкам;

```
Это делается с помощью \Generator, распределяя по времени нагрузку на обмен данных между базой и приложением

$builder = MyModel::chunks();
$builder->chunksModelNativeForeach(
    $limitChunk = 25, $limit = null, $offset = null
);
foreach ( $builder->chunksForeach() as $chunk ) {
    _dump($chunk);
}

$builder = MyModel::chunks();
$builder
    // ->setTotalItems(100)
    // ->setTotalPages(8)
    // ->withSelectCountNative()
    // ->withSelectCountExplain()
    ->paginatePdoNativeForeach(
        $perPage = 13, $page = 7, $pagesDelta = 2,
        $offset = null
    )
;
$result = $builder->paginateResult();
```

- при записи данных пользоваться Persistence, чтобы все запросы выполнялись после того, как логика действия была выполнена - это уменьшит время транзакции, а значит и количество блокировок;

```
$my = MyModel::new();
$my->name = 'any_data';
$my->persistForSaveRecursive();

\Gzhegow\Orm\Core\Orm::getEloquentPersistence()->flush();
```

- указывая связи для загрузки использовать инструмент `ModelClass::relationDot()` передавая туда callable (в этом случае, если вы переименуете метод связи - оно будет переименовано во всем коде, как callable)

```
// НЕ ВЕРНО:
$query->with([
    '_relationName._relationNameChild',
    '_relationName._relationNameChild',
    '_relationName._relationNameChild',
]);
$model->load([
    '_relationName._relationNameChild',
    '_relationName._relationNameChild',
    '_relationName._relationNameChild',
]);
$collection->load([
    '_relationName._relationNameChild',
    '_relationName._relationNameChild',
    '_relationName._relationNameChild',
]);

// ВЕРНО:
$query->with([
    Orm::relationDot()([ ModelClass::class, '_relationName' ])([ Model2Class::class, '_relationNameChild' ])(),
    Orm::relationDot()([ ModelClass::class, '_relationName' ])([ Model2Class::class, '_relationNameChild' ])(),
    Orm::relationDot()([ ModelClass::class, '_relationName' ])([ Model2Class::class, '_relationNameChild' ])(),
]);
$model->load([
    Orm::relationDot()([ ModelClass::class, '_relationName' ])([ Model2Class::class, '_relationNameChild' ])(),
    Orm::relationDot()([ ModelClass::class, '_relationName' ])([ Model2Class::class, '_relationNameChild' ])(),
    Orm::relationDot()([ ModelClass::class, '_relationName' ])([ Model2Class::class, '_relationNameChild' ])(),
]);
$collection->load([
    Orm::relationDot()([ ModelClass::class, '_relationName' ])([ Model2Class::class, '_relationNameChild' ])(),
    Orm::relationDot()([ ModelClass::class, '_relationName' ])([ Model2Class::class, '_relationNameChild' ])(),
    Orm::relationDot()([ ModelClass::class, '_relationName' ])([ Model2Class::class, '_relationNameChild' ])(),
]);
```

## Установить

```
composer require gzhegow/orm
```

## Запустить тесты

```
php test.php
```

## Примеры и тесты

```php
<?php

// > настраиваем PHP
\Gzhegow\Lib\Lib::entrypoint()
    ->setDirRoot(__DIR__ . '/..')
    ->useAll()
;



// > добавляем несколько функция для тестирования
$ffn = new class {
    function root()
    {
        return realpath(__DIR__ . '/..');
    }


    function value_array($value, ?int $maxLevel = null, array $options = []) : string
    {
        return \Gzhegow\Lib\Lib::debug()->dump_value_array($value, $maxLevel, $options);
    }

    function value_array_multiline($value, ?int $maxLevel = null, array $options = []) : string
    {
        return \Gzhegow\Lib\Lib::debug()->dump_value_array_multiline($value, $maxLevel, $options);
    }


    function types($separator = null, ...$values) : string
    {
        return \Gzhegow\Lib\Lib::debug()->dump_types([], $separator, ...$values);
    }

    function values($separator = null, ...$values) : string
    {
        return \Gzhegow\Lib\Lib::debug()->dump_values([], $separator, ...$values);
    }


    function print(...$values) : void
    {
        echo $this->values(' | ', ...$values) . PHP_EOL;
    }

    function print_types(...$values) : void
    {
        echo $this->types(' | ', ...$values) . PHP_EOL;
    }


    function print_array($value, ?int $maxLevel = null, array $options = []) : void
    {
        echo $this->value_array($value, $maxLevel, $options) . PHP_EOL;
    }

    function print_array_multiline($value, ?int $maxLevel = null, array $options = []) : void
    {
        echo $this->value_array_multiline($value, $maxLevel, $options) . PHP_EOL;
    }


    function test(\Closure $fn, array $args = []) : \Gzhegow\Lib\Modules\Test\TestCase
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);

        return \Gzhegow\Lib\Lib::test()->newTestCase()
            ->fn($fn, $args)
            ->trace($trace)
        ;
    }
};



// >>> ЗАПУСКАЕМ!

// > сначала всегда фабрика
$factory = new \Gzhegow\Orm\Core\OrmFactory();

// > создаем сборщик
$builder = $factory->newBuilder();
$builder
    ->defaultStringLength(150)
    //
    // > добавить соединения с БД
    // ->addConnectionDefault([])
    ->addConnection(
        'default',
        [
            0         => 'mysql:host=localhost;port=3306;dbname=test;charset=utf8mb4',
            // 'dsn'             => 'mysql:host=localhost;port=3306;dbname=test;charset=utf8mb4',
            //
            1         => 'root',
            // 'username'        => 'root',
            //
            2         => '',
            // 'password'        => '',
            //
            3         => [
                // > always throw an exception if any error occured
                \PDO::ATTR_ERRMODE           => \PDO::ERRMODE_EXCEPTION,
                //
                // > calculate $pdo->prepare() on PHP level instead of sending it to MySQL as is
                \PDO::ATTR_EMULATE_PREPARES  => true,
                //
                // > since (PHP_VERSION_ID > 80100) mysql `integer` returns integer
                // > setting ATTR_STRINGIFY_FETCHES flag to TRUE forces returning numeric string
                \PDO::ATTR_STRINGIFY_FETCHES => true,
            ],
            // 'pdo_options_new' => [],
            //
            'collate' => 'utf8mb4_unicode_ci',
            //
            'read'    => [
                [ 'host' => 'localhost' ],
            ],
            'write'   => [
                [ 'host' => 'localhost' ],
            ],
        ]
    )
    //
    // > или по-старинке...
    // ->fnInit(
    //     static function ($eloquent) {
    //         $eloquent->addConnection(
    //             [
    //                 'driver' => 'mysql',
    //
    //                 'host' => 'localhost',
    //                 'port' => 3306,
    //
    //                 'username' => 'root',
    //                 'password' => '',
    //
    //                 'database' => 'test',
    //
    //                 'charset'   => 'utf8mb4',
    //                 'collation' => 'utf8mb4_unicode_ci',
    //
    //                 'options' => [
    //                     // > always throw an exception if any error occured
    //                     \PDO::ATTR_ERRMODE           => \PDO::ERRMODE_EXCEPTION,
    //                     //
    //                     // > calculate $pdo->prepare() on PHP level instead of sending it to MySQL as is
    //                     \PDO::ATTR_EMULATE_PREPARES  => true,
    //                     //
    //                     // > since (PHP_VERSION_ID > 80100) mysql `integer` returns integer
    //                     // > setting ATTR_STRINGIFY_FETCHES flag to TRUE forces returning numeric string
    //                     \PDO::ATTR_STRINGIFY_FETCHES => true,
    //                 ],
    //
    //                 'read'  => [
    //                     [ 'host' => 'localhost' ],
    //                 ],
    //                 'write' => [
    //                     [ 'host' => 'localhost' ],
    //                 ],
    //             ],
    //             $connName = 'default'
    //         );
    //     }
    // )
    // //
    // // > выполнить сразу после инициализации Eloquent
    // ->fnBoot(
    //     static function ($eloquent) {
    //         //
    //     }
    // )
    // //
    // // > включаем логирование запросов
    // ->fnLog(
    //     static function ($query) use ($ffn) {
    //         $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 7);
    //         $trace = array_slice($trace, 6);
    //
    //         $sql = preg_replace('~\s+~', ' ', trim($query->sql));
    //         $bindings = $query->bindings;
    //
    //         $files = [];
    //         foreach ( $trace as $item ) {
    //             $traceFile = $item[ 'file' ] ?? '';
    //             $traceLine = $item[ 'line' ] ?? '';
    //
    //             if (! $traceFile) continue;
    //
    //             // > таким образом можно фильтровать список файлов при дебаге, в каком запросе ошибка
    //             // if (false !== strpos($traceFile, '/vendor/')) continue;
    //
    //             $files[] = "{$traceFile}: $traceLine";
    //         }
    //
    //         $dump = [
    //             'sql'      => $sql,
    //             'bindings' => $bindings,
    //             'files'    => $files,
    //         ];
    //
    //         $ffn->print_array_multiline($dump);
    //     }
    // )
;

// > создаем фасад
$facade = $builder->make();

// > устанавливаем фасад
\Gzhegow\Orm\Core\Orm::setFacade($facade);


$eloquent = $facade->getEloquent();
$conn = $eloquent->getConnection();
$schema = $eloquent->getSchemaBuilder($conn);

$modelClassDemoBar = \Gzhegow\Orm\Demo\Model\DemoBarModel::class;
$modelClassDemoBaz = \Gzhegow\Orm\Demo\Model\DemoBazModel::class;
$modelClassDemoFoo = \Gzhegow\Orm\Demo\Model\DemoFooModel::class;
$modelClassDemoImage = \Gzhegow\Orm\Demo\Model\DemoImageModel::class;
$modelClassDemoPost = \Gzhegow\Orm\Demo\Model\DemoPostModel::class;
$modelClassDemoTag = \Gzhegow\Orm\Demo\Model\DemoTagModel::class;
$modelClassDemoUser = \Gzhegow\Orm\Demo\Model\DemoUserModel::class;

$tableDemoBar = $modelClassDemoBar::table();
$tableDemoBaz = $modelClassDemoBaz::table();
$tableDemoFoo = $modelClassDemoFoo::table();
$tableDemoImage = $modelClassDemoImage::table();
$tableDemoPost = $modelClassDemoUser::table();
$tableDemoTag = $modelClassDemoTag::table();
$tableDemoUser = $modelClassDemoPost::table();
$tableTaggable = $modelClassDemoTag::tableMorphedByMany('taggable');


// > удаляем таблицы с прошлого раза
$schema->disableForeignKeyConstraints();
$schema->dropAllTables();
$schema->enableForeignKeyConstraints();


// > создаем таблицы поновой
$schema->create(
    $tableDemoFoo,
    static function (\Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint $blueprint) {
        $blueprint->bigIncrements('id');
        //
        $blueprint->string('name')->nullable();
    }
);

$schema->create(
    $tableDemoBar,
    static function (\Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint $blueprint) use (
        $tableDemoFoo
    ) {
        $blueprint->bigIncrements('id');
        //
        $blueprint->unsignedBigInteger($tableDemoFoo . '_id')->nullable();
        //
        $blueprint->string('name')->nullable();

        $blueprint
            ->foreign($tableDemoFoo . '_id')
            ->references('id')
            ->on($tableDemoFoo)
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE')
        ;
    }
);

$schema->create(
    $tableDemoBaz,
    static function (\Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint $blueprint) use (
        $tableDemoBar
    ) {
        $blueprint->bigIncrements('id');
        //
        $blueprint->unsignedBigInteger($tableDemoBar . '_id')->nullable();
        //
        $blueprint->string('name')->nullable();

        $blueprint
            ->foreign($tableDemoBar . '_id')
            ->references('id')
            ->on($tableDemoBar)
            ->onUpdate('CASCADE')
            ->onDelete('CASCADE')
        ;
    }
);

$schema->create(
    $tableDemoImage,
    static function (\Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint $blueprint) use (
        $tableDemoImage
    ) {
        $blueprint->bigIncrements('id');
        //
        $blueprint->nullableMorphs('imageable');
        //
        $blueprint->string('name')->nullable();
    }
);

$schema->create(
    $tableDemoPost,
    static function (\Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint $blueprint) use (
        $tableDemoPost
    ) {
        $blueprint->bigIncrements('id');
        //
        $blueprint->string('name')->nullable();
    }
);

$schema->create(
    $tableDemoUser,
    static function (\Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint $blueprint) use (
        $tableDemoUser
    ) {
        $blueprint->bigIncrements('id');
        //
        $blueprint->string('name')->nullable();
    }
);

$schema->create(
    $tableDemoTag,
    static function (\Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint $blueprint) use (
        $tableDemoTag
    ) {
        $blueprint->bigIncrements('id');
        //
        $blueprint->string('name')->nullable();
    }
);

$schema->create(
    $tableTaggable,
    static function (\Gzhegow\Orm\Package\Illuminate\Database\Schema\EloquentSchemaBlueprint $blueprint) use (
        $tableTaggable
    ) {
        $blueprint->bigInteger('tag_id')->nullable()->unsigned();
        //
        $blueprint->nullableMorphs('taggable');
    }
);


// >>> ТЕСТЫ

// > TEST
// > используем рекурсивное сохранение для того, чтобы сохранить модели вместе со связями
$fn = function () use (
    $eloquent,
    $schema,
    $ffn
) {
    $ffn->print('[ TEST 1 ]');
    echo PHP_EOL;


    $modelClassDemoFoo = \Gzhegow\Orm\Demo\Model\DemoFooModel::class;
    $modelClassDemoBar = \Gzhegow\Orm\Demo\Model\DemoBarModel::class;
    $modelClassDemoBaz = \Gzhegow\Orm\Demo\Model\DemoBazModel::class;

    $schema->disableForeignKeyConstraints();
    $modelClassDemoFoo::query()->truncate();
    $modelClassDemoBar::query()->truncate();
    $modelClassDemoBaz::query()->truncate();
    $schema->enableForeignKeyConstraints();


    $foo1 = $modelClassDemoFoo::new();
    $foo1->name = 'foo1';
    $bar1 = $modelClassDemoBar::new();
    $bar1->name = 'bar1';
    $baz1 = $modelClassDemoBaz::new();
    $baz1->name = 'baz1';

    $foo2 = $modelClassDemoFoo::new();
    $foo2->name = 'foo2';
    $bar2 = $modelClassDemoBar::new();
    $bar2->name = 'bar2';
    $baz2 = $modelClassDemoBaz::new();
    $baz2->name = 'baz2';


    $bar1->_demoFoo = $foo1;
    $baz1->_demoBar = $bar1;

    $baz1->saveRecursive();


    $bar2->_demoFoo = $foo2;
    $baz2->_demoBar = $bar2;
    $bar2->_demoBazs[] = $baz2;
    $foo2->_demoBars[] = $bar2;

    $foo2->saveRecursive();


    $fooCollection = $modelClassDemoFoo::query()->get([ '*' ]);
    $barCollection = $modelClassDemoBar::query()->get([ '*' ]);
    $bazCollection = $modelClassDemoBaz::query()->get([ '*' ]);

    $ffn->print($fooCollection);
    $ffn->print($fooCollection[ 0 ]->id, $fooCollection[ 1 ]->id);

    $ffn->print($barCollection);
    $ffn->print($barCollection[ 0 ]->id, $barCollection[ 0 ]->demo_foo_id);
    $ffn->print($barCollection[ 1 ]->id, $barCollection[ 1 ]->demo_foo_id);

    $ffn->print($bazCollection);
    $ffn->print($bazCollection[ 0 ]->id, $bazCollection[ 0 ]->demo_bar_id);
    $ffn->print($bazCollection[ 1 ]->id, $bazCollection[ 1 ]->demo_bar_id);
};
$test = $ffn->test($fn);
$test->expectStdout('
"[ TEST 1 ]"

{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
"1" | "2"
{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
"1" | "1"
"2" | "2"
{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
"1" | "1"
"2" | "2"
');
$test->run();


// > TEST
// > используем Persistence для сохранения ранее созданных моделей
// > это нужно, чтобы уменьшить время транзакции - сохранение делаем в конце бизнес-действия
$fn = function () use (
    $eloquent,
    $schema,
    $ffn
) {
    $ffn->print('[ TEST 2 ]');
    echo PHP_EOL;


    $modelClassDemoFoo = \Gzhegow\Orm\Demo\Model\DemoFooModel::class;
    $modelClassDemoBar = \Gzhegow\Orm\Demo\Model\DemoBarModel::class;
    $modelClassDemoBaz = \Gzhegow\Orm\Demo\Model\DemoBazModel::class;

    $schema->disableForeignKeyConstraints();
    $modelClassDemoFoo::query()->truncate();
    $modelClassDemoBar::query()->truncate();
    $modelClassDemoBaz::query()->truncate();
    $schema->enableForeignKeyConstraints();


    $foo3 = $modelClassDemoFoo::new();
    $foo3->name = 'foo3';
    $bar3 = $modelClassDemoBar::new();
    $bar3->name = 'bar3';
    $baz3 = $modelClassDemoBaz::new();
    $baz3->name = 'baz3';

    $foo4 = $modelClassDemoFoo::new();
    $foo4->name = 'foo4';
    $bar4 = $modelClassDemoBar::new();
    $bar4->name = 'bar4';
    $baz4 = $modelClassDemoBaz::new();
    $baz4->name = 'baz4';


    $bar3->_demoFoo = $foo3;
    $baz3->_demoBar = $bar3;

    $baz3->persistForSaveRecursive();


    $bar4->_demoFoo = $foo4;
    $baz4->_demoBar = $bar4;
    $bar4->_demoBazs[] = $baz4;
    $foo4->_demoBars[] = $bar4;

    $foo4->persistForSaveRecursive();


    \Gzhegow\Orm\Core\Orm::persistence()->flush();


    $fooCollection = $modelClassDemoFoo::query()->get([ '*' ]);
    $barCollection = $modelClassDemoBar::query()->get([ '*' ]);
    $bazCollection = $modelClassDemoBaz::query()->get([ '*' ]);

    $ffn->print($fooCollection);
    $ffn->print($fooCollection[ 0 ]->id, $fooCollection[ 1 ]->id);

    $ffn->print($barCollection);
    $ffn->print($barCollection[ 0 ]->id, $barCollection[ 0 ]->demo_foo_id);
    $ffn->print($barCollection[ 1 ]->id, $barCollection[ 1 ]->demo_foo_id);

    $ffn->print($bazCollection);
    $ffn->print($bazCollection[ 0 ]->id, $bazCollection[ 0 ]->demo_bar_id);
    $ffn->print($bazCollection[ 1 ]->id, $bazCollection[ 1 ]->demo_bar_id);
};
$test = $ffn->test($fn);
$test->expectStdout('
"[ TEST 2 ]"

{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
"1" | "2"
{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
"1" | "1"
"2" | "2"
{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
"1" | "1"
"2" | "2"
');
$test->run();


// > TEST
// > тестирование связей (для примера взят Morph), у которых в этом пакете изменился интерфейс создания
$fn = function () use (
    $eloquent,
    $schema,
    $ffn
) {
    $ffn->print('[ TEST 3 ]');
    echo PHP_EOL;


    $modelClassDemoPost = \Gzhegow\Orm\Demo\Model\DemoPostModel::class;
    $modelClassDemoUser = \Gzhegow\Orm\Demo\Model\DemoUserModel::class;
    $modelClassDemoImage = \Gzhegow\Orm\Demo\Model\DemoImageModel::class;
    $modelClassDemoTag = \Gzhegow\Orm\Demo\Model\DemoTagModel::class;

    $schema->disableForeignKeyConstraints();
    $modelClassDemoPost::query()->truncate();
    $modelClassDemoUser::query()->truncate();
    $modelClassDemoImage::query()->truncate();
    $modelClassDemoTag::query()->truncate();
    $schema->enableForeignKeyConstraints();


    $post1 = $modelClassDemoPost::new();
    $post1->name = 'post1';

    $user1 = $modelClassDemoUser::new();
    $user1->name = 'user1';

    $image1 = $modelClassDemoImage::new();
    $image1->name = 'image1';

    $image2 = $modelClassDemoImage::new();
    $image2->name = 'image2';

    $image1->_imageable = $post1;
    $image2->_imageable = $user1;

    $post1->_demoImages[] = $image1;

    $user1->_demoImages[] = $image2;


    $image1->persistForSaveRecursive();
    $image2->persistForSaveRecursive();

    \Gzhegow\Orm\Core\Orm::persistence()->flush();


    $imageQuery = $image1::query()
        ->addColumns($image1->getMorphKeys('imageable'))
        ->with(
            $post1::fnCurryWith()([ $image1, '_imageable' ])()
        )
    ;
    $postQuery = $post1::query()
        ->with(
            $post1::fnCurryWith()([ $post1, '_demoImages' ])()
        )
    ;
    $userQuery = $user1::query()
        ->with(
            $user1::fnCurryWith()([ $user1, '_demoImages' ])()
        )
    ;

    $imageCollection = $modelClassDemoImage::get($imageQuery);
    $postCollection = $modelClassDemoPost::get($postQuery);
    $userCollection = $modelClassDemoUser::get($userQuery);

    $ffn->print($imageCollection);
    $ffn->print($imageCollection[ 0 ], $imageCollection[ 0 ]->_imageable);
    echo PHP_EOL;

    $ffn->print($postCollection);
    $ffn->print($postCollection[ 0 ], $postCollection[ 0 ]->_demoImages[ 0 ]);
    echo PHP_EOL;

    $ffn->print($userCollection);
    $ffn->print($userCollection[ 0 ], $userCollection[ 0 ]->_demoImages[ 0 ]);
    echo PHP_EOL;


    $post2 = $modelClassDemoPost::new();
    $post2->name = 'post2';

    $user2 = $modelClassDemoUser::new();
    $user2->name = 'user2';

    $tag1 = $modelClassDemoTag::new();
    $tag1->name = 'tag1';

    $tag2 = $modelClassDemoTag::new();
    $tag2->name = 'tag2';


    $post2->persistForSave();
    $post2->_demoTags()->persistForSaveMany([
        $tag1,
        $tag2,
    ]);

    $user2->persistForSave();
    $user2->_demoTags()->persistForSaveMany([
        $tag1,
        $tag2,
    ]);

    \Gzhegow\Orm\Core\Orm::persistence()->flush();


    $tagQuery = $modelClassDemoTag::query()
        ->with([
            $modelClassDemoTag::fnCurryWith()([ $modelClassDemoTag, '_demoPosts' ])(),
            $modelClassDemoTag::fnCurryWith()([ $modelClassDemoTag, '_demoUsers' ])(),
        ])
    ;
    $postQuery = $post2::query()
        ->with(
            $post2::fnCurryWith()([ $post2, '_demoTags' ])()
        )
    ;
    $userQuery = $user2::query()
        ->with(
            $user2::fnCurryWith()([ $user2, '_demoTags' ])()
        )
    ;

    $tagCollection = $modelClassDemoTag::get($tagQuery);
    $postCollection = $modelClassDemoPost::get($postQuery);
    $userCollection = $modelClassDemoUser::get($userQuery);

    $ffn->print($tagCollection);
    $ffn->print($tagCollection[ 0 ], $tagCollection[ 0 ]->_demoPosts[ 0 ], $tagCollection[ 0 ]->_demoUsers[ 0 ]);
    $ffn->print($tagCollection[ 1 ], $tagCollection[ 1 ]->_demoPosts[ 0 ], $tagCollection[ 1 ]->_demoUsers[ 0 ]);
    echo PHP_EOL;

    $ffn->print($postCollection);
    $ffn->print($postCollection[ 1 ], $postCollection[ 1 ]->_demoTags[ 0 ], $postCollection[ 1 ]->_demoTags[ 1 ]);
    echo PHP_EOL;

    $ffn->print($userCollection);
    $ffn->print($userCollection[ 1 ], $userCollection[ 1 ]->_demoTags[ 0 ], $userCollection[ 1 ]->_demoTags[ 1 ]);
};
$test = $ffn->test($fn);
$test->expectStdout('
"[ TEST 3 ]"

{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(stringable) # Gzhegow\Orm\Demo\Model\DemoImageModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoPostModel }

{ object(countable(1) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(stringable) # Gzhegow\Orm\Demo\Model\DemoPostModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoImageModel }

{ object(countable(1) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(stringable) # Gzhegow\Orm\Demo\Model\DemoUserModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoImageModel }

{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(stringable) # Gzhegow\Orm\Demo\Model\DemoTagModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoPostModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoUserModel }
{ object(stringable) # Gzhegow\Orm\Demo\Model\DemoTagModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoPostModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoUserModel }

{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(stringable) # Gzhegow\Orm\Demo\Model\DemoPostModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoTagModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoTagModel }

{ object(countable(2) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(stringable) # Gzhegow\Orm\Demo\Model\DemoUserModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoTagModel } | { object(stringable) # Gzhegow\Orm\Demo\Model\DemoTagModel }
');
$test->run();


// > TEST
// > можно подсчитать количество записей в таблице используя EXPLAIN, к сожалению, будет показано число строк, которое придется обработать, а не число строк по результатам запроса
// > но иногда этого достаточно, особенно если запрос покрыт должным числом индексов, чтобы отобразить "Всего: ~100 страниц"
$fn = function () use (
    $eloquent,
    $schema,
    $ffn
) {
    $ffn->print('[ TEST 4 ]');
    echo PHP_EOL;


    $modelClassDemoTag = \Gzhegow\Orm\Demo\Model\DemoTagModel::class;

    $schema->disableForeignKeyConstraints();
    $modelClassDemoTag::query()->truncate();
    $schema->enableForeignKeyConstraints();

    for ( $i = 0; $i < 100; $i++ ) {
        $tag = $modelClassDemoTag::new();
        $tag->name = 'tag' . $i;
        $tag->save();
    }


    $query = $modelClassDemoTag::query()->where('name', 'tag77');
    $ffn->print($cnt = $query->count(), $cnt === 1);

    $cnt = $query->countExplain();
    $ffn->print($cnt > 1, $cnt <= 100);
};
$test = $ffn->test($fn);
$test->expectStdout('
"[ TEST 4 ]"

1 | TRUE
TRUE | TRUE
');
$test->run();


// > TEST
// > используем механизм Chunk, чтобы считать данные из таблиц
// > на базе механизма работает и пагинация, предлагается два варианта - нативный SQL LIMIT/OFFSET и COLUMN(>|>=|<|<=)VALUE
$fn = function () use (
    $eloquent,
    $schema,
    $ffn
) {
    $ffn->print('[ TEST 5 ]');
    echo PHP_EOL;


    $modelClassDemoTag = \Gzhegow\Orm\Demo\Model\DemoTagModel::class;

    $schema->disableForeignKeyConstraints();
    $modelClassDemoTag::query()->truncate();
    $schema->enableForeignKeyConstraints();

    for ( $i = 0; $i < 100; $i++ ) {
        $tag = $modelClassDemoTag::new();
        $tag->name = 'tag' . $i;
        $tag->save();
    }


    $ffn->print('chunkModelNativeForeach');
    $builder = $modelClassDemoTag::chunks();
    $builder->chunksModelNativeForeach(
        $limitChunk = 25, $limit = null, $offset = null
    );
    foreach ( $builder->chunksForeach() as $chunk ) {
        $ffn->print($chunk);
    }
    echo PHP_EOL;


    $ffn->print('chunkModelAfterForeach');
    $builder = $modelClassDemoTag::chunks();
    $builder = $builder->chunksModelAfterForeach(
        $limitChunk = 25, $limit = null,
        $offsetColumn = 'id', $offsetOperator = '>', $offsetValue = 1, $includeOffsetValue = true
    );
    foreach ( $builder->chunksForeach() as $chunk ) {
        $ffn->print($chunk);
    }
};
$test = $ffn->test($fn);
$test->expectStdout('
"[ TEST 5 ]"

"chunkModelNativeForeach"
{ object(countable(25) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(countable(25) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(countable(25) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(countable(25) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }

"chunkModelAfterForeach"
{ object(countable(25) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(countable(25) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(countable(25) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
{ object(countable(25) iterable stringable) # Gzhegow\Orm\Package\Illuminate\Database\Eloquent\EloquentModelCollection }
');
$test->run();


// > TEST
// > используем механизм Chunk, чтобы считать данные из таблиц
// > на базе механизма работает и пагинация, предлагается два варианта - нативный SQL LIMIT/OFFSET и COLUMN(>|>=|<|<=)VALUE
$fn = function () use (
    $eloquent,
    $schema,
    $ffn
) {
    $ffn->print('[ TEST 6 ]');
    echo PHP_EOL;


    $modelClassDemoTag = \Gzhegow\Orm\Demo\Model\DemoTagModel::class;

    $schema->disableForeignKeyConstraints();
    $modelClassDemoTag::query()->truncate();
    $schema->enableForeignKeyConstraints();

    for ( $i = 0; $i < 100; $i++ ) {
        $tag = $modelClassDemoTag::new();
        $tag->name = 'tag' . $i;
        $tag->save();
    }


    $ffn->print('paginateModelNativeForeach');
    $builder = $modelClassDemoTag::chunks();
    $builder
        // ->setTotalItems(100)
        // ->setTotalPages(8)
        // ->withSelectCountNative()
        // ->withSelectCountExplain()
        ->paginatePdoNativeForeach(
            $perPage = 13, $page = 7, $pagesDelta = 2,
            $offset = null
        )
    ;

    $result = $builder->paginateResult();
    $ffn->print_array_multiline((array) $result);
    $ffn->print_array_multiline($result->pagesAbsolute);
    $ffn->print_array_multiline($result->pagesRelative);
    echo PHP_EOL;


    $ffn->print('paginateModelAfterForeach');
    $builder = $modelClassDemoTag::chunks();
    $builder
        // ->setTotalItems(100)
        // ->setTotalPages(8)
        // ->withSelectCountNative()
        // ->withSelectCountExplain()
        ->paginatePdoAfterForeach(
            $perPage = 13, $page = 7, $pagesDelta = 2,
            $offsetColumn = 'id', $offsetOperator = '>', $offsetValue = 1, $includeOffsetValue = true
        )
    ;

    $result = $builder->paginateResult();
    $ffn->print_array_multiline((array) $result);
    $ffn->print_array_multiline($result->pagesAbsolute);
    $ffn->print_array_multiline($result->pagesRelative);
};
$test = $ffn->test($fn);
$test->expectStdout('
"[ TEST 6 ]"

"paginateModelNativeForeach"
###
[
  "totalItems" => 100,
  "totalPages" => 8,
  "page" => 7,
  "perPage" => 13,
  "pagesDelta" => 2,
  "from" => 78,
  "to" => 91,
  "pagesAbsolute" => "{ array(5) }",
  "pagesRelative" => "{ array(5) }",
  "items" => "{ object(countable(13) iterable stringable) # Illuminate\Support\Collection }"
]
###
###
[
  1 => 13,
  5 => 13,
  6 => 13,
  7 => 13,
  8 => 9
]
###
###
[
  "first" => 13,
  "previous" => 13,
  "current" => 13,
  "next" => NULL,
  "last" => 9
]
###

"paginateModelAfterForeach"
###
[
  "totalItems" => 100,
  "totalPages" => 8,
  "page" => 7,
  "perPage" => 13,
  "pagesDelta" => 2,
  "from" => 78,
  "to" => 91,
  "pagesAbsolute" => "{ array(5) }",
  "pagesRelative" => "{ array(5) }",
  "items" => "{ object(countable(13) iterable stringable) # Illuminate\Support\Collection }"
]
###
###
[
  1 => 13,
  5 => 13,
  6 => 13,
  7 => 13,
  8 => 9
]
###
###
[
  "first" => 13,
  "previous" => 13,
  "current" => 13,
  "next" => NULL,
  "last" => 9
]
###
');
$test->run();


// > TEST
// > рекомендуется в проекте указывать связи в виде callable, чтобы они менялись, когда применяешь `Refactor` в PHPStorm
$fn = function () use (
    $eloquent,
    $ffn
) {
    $ffn->print('[ TEST 7 ]');
    echo PHP_EOL;


    $foo_hasMany_bars_hasMany_bazs = \Gzhegow\Orm\Core\Orm::fnCurryWith()
    ([ \Gzhegow\Orm\Demo\Model\DemoFooModel::class, '_demoBars' ])
    ([ \Gzhegow\Orm\Demo\Model\DemoBarModel::class, '_demoBazs' ])
    ();
    $ffn->print($foo_hasMany_bars_hasMany_bazs);

    $bar_belongsTo_foo = \Gzhegow\Orm\Demo\Model\DemoBarModel::fnCurryWith()
    ([ \Gzhegow\Orm\Demo\Model\DemoBarModel::class, '_demoFoo' ])
    ();
    $ffn->print($bar_belongsTo_foo);

    $bar_hasMany_bazs = \Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel::fnCurryWith()
    ([ \Gzhegow\Orm\Demo\Model\DemoBarModel::class, '_demoBazs' ])
    ();
    $ffn->print($bar_hasMany_bazs);

    $bar_belongsTo_foo_only_id = \Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel::fnCurryWith()
    ([ \Gzhegow\Orm\Demo\Model\DemoBarModel::class, '_demoFoo' ], 'id')
    ();
    $ffn->print($bar_belongsTo_foo_only_id);

    $bar_hasMany_bazs_only_id = \Gzhegow\Orm\Package\Illuminate\Database\Eloquent\Base\AbstractEloquentModel::fnCurryWith()
    ([ \Gzhegow\Orm\Demo\Model\DemoBarModel::class, '_demoBazs' ], 'id')
    ();
    $ffn->print($bar_hasMany_bazs_only_id);

    // > ПРИМЕР
    // > Делаем запрос со связями
    // $query = \Gzhegow\Orm\Demo\Model\DemoFooModel::query();
    // $query->with($foo_hasMany_bars_hasMany_bazs);
    // $query->with([
    //     $foo_hasMany_bars_hasMany_bazs,
    // ]);
    // $query->with([
    //     $foo_hasMany_bars_hasMany_bazs => static function ($query) { },
    // ]);
    //
    // $query = \Gzhegow\Orm\Demo\Model\DemoBarModel::query();
    // $query->with($bar_belongsTo_foo);
    // $query->with([
    //     $bar_belongsTo_foo,
    //     $bar_hasMany_bazs,
    // ]);
    // $query->with([
    //     $bar_belongsTo_foo => static function ($query) { },
    //     $bar_hasMany_bazs  => static function ($query) { },
    // ]);

    // > Подгружаем связи к уже полученным из базы моделям
    // $query = \Gzhegow\Orm\Demo\Model\DemoFooModel::query();
    // $model = $query->firstOrFail();
    // $model->load($foo_hasMany_bars_hasMany_bazs);
    // $model->load([
    //     $foo_hasMany_bars_hasMany_bazs,
    // ]);
    // $model->load([
    //     $foo_hasMany_bars_hasMany_bazs => static function ($query) { },
    // ]);
    //
    // $query = \Gzhegow\Orm\Demo\Model\DemoBarModel::query();
    // $model = $query->firstOrFail();
    // $model->load($bar_belongsTo_foo);
    // $model->load([
    //     $bar_belongsTo_foo,
    //     $bar_hasMany_bazs,
    // ]);
    // $model->load([
    //     $bar_belongsTo_foo => static function ($query) { },
    //     $bar_hasMany_bazs  => static function ($query) { },
    // ]);
};
$test = $ffn->test($fn);
$test->expectStdout('
"[ TEST 7 ]"

"_demoBars._demoBazs"
"_demoFoo"
"_demoBazs"
"_demoFoo:id"
"_demoBazs:id"
');
$test->run();


// > удаляем таблицы после тестов
$schema->disableForeignKeyConstraints();
$schema->dropAllTables();
$schema->enableForeignKeyConstraints();
```

