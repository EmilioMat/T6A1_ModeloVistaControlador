<?php

namespace App\Models;

/**
 * Gestiona la conexión de la base de datos e incluye un esquema para
 * un Query Builder. Los return son ejemplo en caso de consultar la tabla
 * usuarios.
 */
require_once '../vendor/autoload.php';
$dotenv = \Dotenv\Dotenv::createImmutable('..');
$dotenv->load();

class Model
{

    private $connection;

    private $query; // Consulta a ejecutar

    private $select = '*';
    private $where, $values = [];
    private $orderBy;
    protected $table1;
    protected $table2; // Definido en el hijo

    public function __construct()
    {

        $this->connection();
    }

    private function connection(): void
    {
        $dbHost = $_ENV['DB_HOST'];
        $dbName = $_ENV['DB_NAME'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASS'];
        try {
            $dsn = "mysql:host={$dbHost};dbname={$dbName}";
            $this->connection = new \PDO($dsn, $dbUser, $dbPass);
            $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    // QUERY BUILDER
    // Consultas: 

    // Recibe la cadena de consulta y la ejecuta
    private function query(string $sql, array $data = []): object
    {


        // Si hay $data se lanzará una consulta preparada, en otro caso una normal
        if ($data) {

            $stmp = $this->connection->prepare($sql);

            // Vincular los parámetros dinámicamente
            foreach ($data as $key => $value) {

                $stmp->bindValue($key + 1, $value);
            }

            $stmp->execute();
        } else {
            $this->query = $this->connection->query($sql);
        }


        return $this;
    }

    public function select(string ...$columns): object
    {
        // Separamos el array en una cadena con ,
        $this->select = implode(', ', $columns);

        return $this;
    }

    // Devuelve todos los registros de una tabla
    public function all(): array
    {
        // La consulta sería
        $sql = "SELECT * FROM {$this->table1}";
        // Y se llama a la sentencia
        $this->query($sql)->get();
        // para obtener los datos del select
        return $this->query->fetchall(\PDO::FETCH_ASSOC);
    }

    // Consulta base a la que se irán añadiendo partes
    public function get(): array
    {
        if (empty($this->query)) {
            $sql = "SELECT {$this->select} FROM {$this->table1}";

            // Se comprueban si están definidos para añadirlos a la cadena $sql
            if ($this->where) {
                $sql .= " WHERE {$this->where}";
            }

            if ($this->orderBy) {
                $sql .= " ORDER BY {$this->orderBy}";
            }

            $this->query = $this->connection->prepare($sql);
            $this->query->execute($this->values);
            //para obtener los datos del select
            return $this->query->fetchall(\PDO::FETCH_ASSOC);
        }
    }

    public function find(int $id): array
    {
        $sql = "SELECT * FROM {$this->table1} WHERE id = ?";

        $this->query = $this->connection->prepare($sql);
        $this->query->execute([$id]);
        return $this->query->fetch(\PDO::FETCH_ASSOC);
    }

    // Se añade where a la sentencia con operador específico
    public function where(string $column, string $operator, string $value = null, string $chainType = 'AND'): object
    {
        if ($value == null) { // Si no se pasa operador, por defecto =
            $value = $operator;
            $operator = '=';
        }

        // Si ya había algo de antes 
        if ($this->where) {
            $this->where .= " {$chainType} {$column} {$operator} ?";
        } else {
            $this->where = "{$column} {$operator} ?";
        }

        $this->values[] = $value;

        return $this;
    }

    // Se añade orderBy a la sentencia
    public function orderBy(string $column, string $order = 'ASC'): object
    {
        if ($this->orderBy) {
            $this->orderBy .= ", {$column} {$order}";
        } else {
            $this->orderBy = "{$column} {$order}";
        }

        return $this;
    }

    // Insertar, recibimos un $_GET o $_POST en $data el parametro table es para definir en que tabla insertamos
    public function create(array $data, string $table): object
    {
        $columns = array_keys($data); // array de claves del array
        $columns = implode(', ', $columns); // y creamos una cadena separada por ,

        $values = array_values($data); // array de los valores

        $sql = "INSERT INTO {$table} ({$columns}) VALUES (?" . str_repeat(', ? ', count($values) - 1) . ")";

        $this->query($sql, $values);

        return $this;
    }

    public function update(int $id, array $data): object
    {
        $fields = [];

        foreach ($data as $key => $value) {
            $fields[] = "{$key} = ?";
        }

        $fields = implode(', ', $fields);

        $sql = "UPDATE {$this->table1} SET {$fields} WHERE id = ?";

        $values = array_values($data);
        $values[] = $id;

        $this->query($sql, $values);
        return $this;
    }

    public function delete(int $id): void
    //delete se realizara en la tabla uno solamente ya que las siguiente se borraría en cascada.
    {
        $sql = "DELETE FROM {$this->table1} WHERE id = ?";

        $this->query($sql, [$id], 'i');
    }
    // en este metodo llamamos dentro al select y las claves para comparar y hacer el join las pasamos desde fuera
    public function innerJoin(string $primaria, string $foranea): array
    {
        //consulta
        $sql = "SELECT {$this->select} FROM {$this->table1} INNER JOIN {$this->table2} ON {$primaria}={$foranea}";
        $this->query = $this->connection->prepare($sql);
        
        $this->query->execute();
        //para obtener los datos del select
        return $this->query->fetchall(\PDO::FETCH_ASSOC);
    }
    // pasamos $data 1 que serian los campos correspondientes al objeto padre producto producto->getnombre, etc...
    //data2 sería pro ejmplo solo la talla en el caso de ropa ropa->get
    public function crearProducto(array $data1, array $data2): string {
        try {
            $this->connection->beginTransaction();
             //data1 sería el equivalente a los valores de la tabla1 Id, nombre, precio
            $this->create($data1, $this->table1);
            /* data2 sería el equivalente a los valores de la tabla2 en este caso solo talla u otros equivalentes segun el producto
            ya que id_producto viene definido por el insert anterior */
             // definimos y concatenamos aquí el lastInsertID()
            $lastInsertedId = $this->connection->lastInsertId();
            $data2 ['id_producto']= $lastInsertedId;
            $this->create($data2, $this->table2);
            $this->connection->commit();
            //  echos para pruebas en la app se gestionara con un mensaje en pantalla
            $mensaje = "El nuevo producto se registró correctamente";
            return $mensaje;
             } catch (\Exception $e) {
                // deshacemos la transacción 
                $this->connection->rollback();
                $error = "Error en el registro" . $e->getMessage();
               return $error;
            }
       
        
       

    }

    // en este metodo llamamos dentro al select y las claves para comparar y hacer el join las pasamos desde fuera
    public function innerJoinFind(string $primaria, string $foranea, int $id): array
    {
        //consulta
        $sql = "SELECT {$this->select} FROM {$this->table1} INNER JOIN {$this->table2} ON {$primaria}={$foranea} WHERE productos.id =?";
        $this->query = $this->connection->prepare($sql);
        
        $this->query->execute([$id]);
        //para obtener los datos del select
        return $this->query->fetch(\PDO::FETCH_ASSOC);
    }
    public function actualizarDatos(array $data):object{
        

        $sql = "CALL actualizar_datos(?, ?, ?, ?)";

        $values = array_values($data);

        $this->query($sql, $values);
        return $this;
    
    }
    public function cambiarTallas(string $talla): array{
        //con este procedimiento cambiamos la talla de toda la ropa de una talla seleccionada a XL que deseemos 
        //solo hay que pasarle la talla 
        $sql = "SET @mi_talla = ?";
        $this->query = $this->connection->prepare($sql);
        $this->query->execute([$talla]);
      
        $sql = "CALL cambiar_talla(@mi_talla, @cantidad)";
    
        
        $this->query = $this->connection->prepare($sql);
        $this->query->execute();
    
        // Recuperar el valor de los parámetros OUT/INOUT
        $sql = "SELECT @mi_talla AS nueva_talla, @cantidad";
        $this->query = $this->connection->prepare($sql);
        $this->query->execute();
    
        
        return $this->query->fetch(\PDO::FETCH_ASSOC);
    }
    
    
}
