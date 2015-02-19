<?php

namespace PHPixieTests\ORM;

abstract class FunctionalTest extends \PHPixieTests\AbstractORMTest
{
    protected $databaseConfigData = array(
        'default' => array(
            'driver' => 'pdo',
            'connection' => 'sqlite::memory:'
        )
    );
    
    protected $ormConfigData = array();
    protected $database;
    protected $wrappers;
    
    protected $orm;
    
    public function setUp()
    {
        $config = new \PHPixie\Config();
        
        $dbConfig = $config->dataStorage($this->databaseConfigData);
        $ormConfig = $config->dataStorage($this->ormConfigData);
        
        $this->database = new \PHPixie\Database($dbConfig);
        $this->orm = new \PHPixie\ORM(
            $this->database,
            $ormConfig,
            $this->wrappers
        );
        
        $this->createDatabase();
        
        $this->fairiesRepository = $this->orm->geT('fairy');
    }
    

    protected function createEntity($name, $data)
    {
        $entity = $this->orm->get($name)->create();
        foreach($data as $field => $value) {
            $entity->$field = $value;
        }
        $entity->save();
        
        return $entity;
    }
    
    protected function assertEntities($data, $entities, $idField = null)
    {
        $this->assertSame(count($data), count($entities));
        
        foreach($entities as $key => $entity) {
            $this->assertEntity($entity, $data[$key], $idField);
        }
    }
    
    protected function assertData($modelName, $data, $idField = null)
    {
        $entities = $this->orm->get($modelName)->query()
                        ->find()
                        ->asArray();
        
        $this->assertEntities($data, $entities);
    }
    
    protected function assertEntity($entity, $data, $idField = null)
    {
        if($idField) {
            $id = $data[$idField];
            $this->assertEquals($id, $entity->id());
        }

        foreach($data as $field => $value) {
            $this->assertEquals($value, $entity->$field);
        }
    }
    
    abstract protected function createDatabase();

}