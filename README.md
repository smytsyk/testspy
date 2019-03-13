
# How it works

## **1. Informer**
First process should be for keeping test matrix up to date. This script has to be executed after every merge to "master" branch. 
It requires implementing TestRunnerInterface. Basically command to run all existing tests in the repository.

```
$syncControl = new SyncControl(new Redis_TestSpy_Dao());

$testRunner = new Test_Runner();

$matrixUpdater = new MatrixUpdater($syncControl, $testRunner);
$matrixUpdater->update();
```

**Listener**

TestSpy listener has to be included in PHPUnit xml config. It will allow to check which files are involved while a particular test is running and store it to the storage. Tests with this listener should be triggered in "Informer".
```
    <listener class="\TestSpy\SpyListener">
      <arguments>
        <object class="\Tests\Listeners\Redis_TestSpy_Dao"/>

        <!--Absolute path to the repository-->
        <string>/codebase/php/</string>
      </arguments>
    </listener>
```

## **2. Spy**
This process is responsible to generate config file with the list of tests which are required to be run for the change in the current branch in comparison with master branch.

It will provide the path to the default config if TestSpy cannot access the storage.
It requires to specify:
- path to repository which is going to be under the tests
- path to the bootstrap.php

```
$syncControl = new SyncControl(new Redis_TestSpy_Dao());

$isTestSpyReady = $syncControl->isReady();

if ($isTestSpyReady === false) {
  echo 'default/path/to/config/with/all/tests.xml';
  exit();
}

$pathToRepo     = '/codebase/php_copy';
$branchDetector = new BranchDetector($pathToRepo, new Utilities());

$changedFiles      = $branchDetector->getChangedFiles();
$pathToBaseUnitXml = INCLUDE_PATH . '/tests/conf/phpunit.xml';

$runner = new Runner(
    new Redis_TestSpy_Dao(),
    new PhpUnitConfigBuilder($pathToBaseUnitXml),
    new Utilities(),
    new RunnerConfig(INCLUDE_PATH . '/tests/conf/bootstrap.php')
);

echo $runner->buildConfig(...$changedFiles) . PHP_EOL;
```

## **3. Config**
Returned config should be used to run tests.

# Internal doc

## How to run Unit tests
`vendor/bin/phpunit --bootstrap bootstrap.php tests/Unit`


### How to run Integration tests
`vendor/bin/phpunit --bootstrap bootstrap.php tests/Integration`

## **4. Parallel Runner**
Multi-threading for testSpy runner. There is a way to configure how many threads to run and how many tests per execution.

## Config: 
`src/Parallel/Config/Resources/Config.yml`

## How to Run:

`./parallel.php refresh` - default configuration
`./parallel.php 2 10` Optional configuration. Usage: refresh [<threads>] [<batch-size>]

## Lock
It allows to run only one runner at time. In case something goes wrong, you need to release a lock manually:
`rm pathfrom_config/testSpy-sync-run-lock`