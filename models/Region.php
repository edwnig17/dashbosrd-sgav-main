<?php 
    namespace Models;
    class Region{
        protected static $conn;
        protected static $columnsTbl=['id_region','name_region','id_country'];
        private $id_region;
        private $name_region;
        private $id_country;
        public function __construct($args=[]){
            $this->id_region = $args['id_region'] ?? '';
            $this->name_region = $args['name_region'] ?? '';
            $this->id_country = $args['id_country'] ?? '';            
        }
        public function saveData($data){
            $delimiter = ":";
            $dataBd = $this->sanitizarAttributos();
            $valCols = $delimiter . join(',:',array_keys($data));
            $cols = join(',',array_keys($data));
            $sql = "INSERT INTO regions ($cols) VALUES ($valCols)";
            $stmt= self::$conn->prepare($sql);
            try {
                $stmt->execute($data);
                $response=[[
                    'id_region' => self::$conn->lastInsertId(),
                    'name_region' => $data['name_region'],
                    'id_country' => $data['id_country']
                ]];
            }catch(\PDOException $e) {
                return $sql . "<br>" . $e->getMessage();
            }
            return json_encode($response);
        }       
        public function loadAllData(){
            $sql = "SELECT id_region,name_region,id_country FROM regions";
            $stmt= self::$conn->prepare($sql);
            $stmt->execute();
            $countries = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $countries;
        }
        public function deleteData($id){
            $sql = "DELETE FROM regions where id_region = :id";
            $stmt= self::$conn->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
        public function updateData($data){
            $sql = "UPDATE regions SET name_region = :name_region, id_country = :id_country where id_region = :id_region";
            $stmt= self::$conn->prepare($sql);
            $stmt->bindParam(':name_region', $data['name_region']);
            $stmt->bindParam(':id_country', $data['id_country']);
            $stmt->bindParam(':id_region', $data['id_region']);
            $stmt->execute();
        }
        public static function setConn($connBd){
            self::$conn = $connBd;
        }
        public function atributos(){
            $atributos = [];
            foreach (self::$columnsTbl as $columna){
                if($columna === 'id_region') continue;
                $atributos [$columna]=$this->$columna;
             }
             return $atributos;
        }
        public function sanitizarAttributos(){
            $atributos = $this->atributos();
            $sanitizado = [];
            foreach($atributos as $key => $value){
                $sanitizado[$key] = self::$conn->quote($value);
            }
            return $sanitizado;
        }
    }
?>