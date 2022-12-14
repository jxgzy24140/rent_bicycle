<?php
include_once($_SERVER["DOCUMENT_ROOT"] . '/rent_bicycle/Models/StoreModel.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/rent_bicycle/Models/BicycleModelModel.php');
include_once($_SERVER["DOCUMENT_ROOT"] . '/rent_bicycle/connection/Connector.php');
class StoreDAO
{
    public Store $store;
    public static $conn;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public static function updateStore(Store $store)
    {
        StoreDAO::$conn = Connector::Connect();
        try {
            $stmt = StoreDAO::$conn->prepare("UPDATE Store SET Address = ? WHERE UniqueName = ?");
            $stmt->bind_param("ss", $store->address, $store->uniqueName);
            $stmt->execute();
            if($stmt->affected_rows == 0)
                return false;
            return true;
        } catch (Exception $th) {
            //throw $th;
            echo $th;
        }
    }

    public function insert()
    {
        StoreDAO::$conn = Connector::Connect();
        try {
            $stmt = StoreDAO::$conn->prepare("INSERT INTO STORE VALUES(?, ?)");
            $stmt->bind_param("ss", $this->store->uniqueName, $this->store->address);
            return $stmt->execute();
            
        } catch (Exception $th) {
            //throw $th;
            echo $th;
        }
    }

    public static function insertStore(Store $store)
    {
        StoreDAO::$conn = Connector::Connect();
        try {
            $stmt = StoreDAO::$conn->prepare("INSERT INTO STORE VALUES(?, ?)");
            $stmt->bind_param("ss", $store->uniqueName, $store->address);
            return $stmt->execute();
            
        } catch (Exception $th) {
            //throw $th;
            echo $th;
        }
    }

    public static function getAllStore()
    {
        StoreDAO::$conn = Connector::Connect();
        try {
            $stmt = StoreDAO::$conn->prepare("SELECT * FROM STORE");
            $stmt->execute();
            $result = $stmt->get_result();
            $listStores = [];
            while($row = $result->fetch_assoc())
            {
                $listStores[] = new Store($row);
            }
            return $listStores;
        } catch (Exception $th) {
            //throw $th;
            echo $th;
        }
    }

    public static function getStoreByAddress($address)
    {
        $address = "%$address%";
        StoreDAO::$conn = Connector::Connect();
        try {
            $stmt = StoreDAO::$conn->prepare("  SELECT * FROM STORE WHERE Address LIKE ? LIMIT 1
                                            ");
            $stmt->bind_param("s", $address);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows == 0)
                return null;
            $data = $result->fetch_assoc();
            return new Store($data);
        } catch (\Throwable $th) {
            //throw $th;
            echo $th;
        }
    }

    public static function getAllBicycleModelBelongToStore($address)
    {
        $address = "%$address%";
        StoreDAO::$conn = Connector::Connect();
        try {
            $stmt = StoreDAO::$conn->prepare("  SELECT Store_BicycleModel.Name_BicycleModel AS UniqueName, 
                                                        BicycleModel.Type, BicycleModel.Gear, BicycleModel.image 
                                                FROM Store_BicycleModel, BicycleModel, Store
                                                WHERE Store_BicycleModel.Name_BicycleModel = BicycleModel.UniqueName
                                                AND Store_BicycleModel.Name_Store = Store.UniqueName
                                                AND Store.Address LIKE ?
                                            ");
            $stmt->bind_param("s", $address);
            $stmt->execute();
            $result = $stmt->get_result();
            $listBicycleModels = [];
            while($row = $result->fetch_assoc())
            {
                $listBicycleModels[] = new BicycleModel($row);
            }
            return $listBicycleModels;
        } catch (\Throwable $th) {
            //throw $th;
            echo $th;
        }
    }

    public static function getAllStoreHasBicycleModel($nameBicycleModel)
    {
        StoreDAO::$conn = Connector::Connect();
        try {
            $stmt = StoreDAO::$conn->prepare("  SELECT SM.Name_Store AS UniqueName, S.Address AS Address
                                                FROM Store_BicycleModel SM, Store S
                                                WHERE SM.Name_Store = S.UniqueName
                                                AND SM.Name_BicycleModel = ?
                                            ");
            $stmt->bind_param("s", $nameBicycleModel);
            $stmt->execute();
            $result = $stmt->get_result();
            $listStores = [];
            while($row = $result->fetch_assoc())
            {
                $listStores[] = new Store($row);
            }
            return $listStores;
        } catch (\Throwable $th) {
            //throw $th;
            echo $th;
        }
    }

    public function getNumberOfBicycleBelongToSpecificModel($nameBicycleModel)
    {
        StoreDAO::$conn = Connector::Connect();
        try {
            $stmt = StoreDAO::$conn->prepare("  SELECT COUNT(Bicycle.IdentifyNumber) AS Quantity
                                                FROM Store_Bicycle, Bicycle
                                                WHERE Store_Bicycle.IdentifyNumber = Bicycle.IdentifyNumber
                                                AND Bicycle.Status = 1
                                                AND Store_Bicycle.Name_Store = ?
                                                AND Bicycle.UniqueName = ?
                                            ");
            $stmt->bind_param("ss", $this->store->uniqueName, $nameBicycleModel);
            $stmt->execute();
            $result = $stmt->get_result();
            $quantity = $result->fetch_assoc();
            return $quantity["Quantity"];
        } catch (\Throwable $th) {
            //throw $th;
            echo $th;
        }
    }

    
}
