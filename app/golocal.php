<?php


namespace CoFund\Console;


use GuzzleHttp\Client;
use CoFund\Common\Db;
use R;

class PepperjamImport
{
    use StoreImportTrait;

    const AFFILIATE_NETWORK_ID = 3; // Pepperjam (Ebay)

    protected $sql = "
INSERT INTO
cofundstore_import (
    network_id,
    adv_store_id,
    adv_store_name,
    adv_store_raw_data,
    adv_store_categories,
    store_status
)
VALUES (
    :network_id,
    :adv_store_id,
    :adv_store_name,
    :adv_store_raw_data,
    :adv_store_categories,
    :store_status
)
ON DUPLICATE KEY UPDATE
    adv_store_name = :adv_store_name,
    adv_store_raw_data = :adv_store_raw_data,
    adv_store_categories = :adv_store_categories,
    store_status = :store_status
    ";

    protected $categorySql = "
INSERT INTO
  cofundnetwork_categories (
    network_id,
    network_category_id,
    network_category_name,
    cofund_category_id,
    last_updated
  )
VALUES (
  :network_id,
  :network_category_id,
  :network_category_name,
  :cofund_category_id,
  :last_updated
)
ON DUPLICATE KEY UPDATE
  network_category_name = :network_category_name,
  last_updated = :last_updated
    ";

    /**
     *
     * "meta": {
     * "pagination": {
     * "next": {
     * "description": "Next Page",
     * "href": "http://api.pepperjamnetwork.com/20120402/publisher/advertiser?apiKey=11ec53f0c84e8bb1fbc88f6bd5eb287180cacea04ad7cdee4fd4f310832dd81d&format=json&page=2",
     * "rel": "next"
     * },
     * "total_pages": 2,
     * "total_results": 976
     * },
     * "requests": {
     * "current": 40,
     * "maximum": 1500
     * },
     * "status": {
     * "code": 200,
     * "message": "OK"
     * }
     * }
     * @var
     */
    protected $meta;

    protected $categories = [];

    protected $api;

    protected $db;

    protected $client;

    public function __construct(\stdClass $api, Db $db, Client $client = null)
    {
        $this->api = $api;
        $this->db  = $db;

        $this->client = $this->client = ($client instanceof Client) ? $client : new Client([
            'http_errors' => false
        ]);
    }

    public function run()
    {
        echo 'Pepperjam Import starting...' . PHP_EOL;

        $stores = $this->load(date('Y-m-d'));

        if (false === $stores) {

            $stores = $this->getAllStores($this->api->url, [
                'query' => [
                    'apiKey' => $this->api->key,
                    'format' => 'json',
                    'page'   => 1
                ]
            ]);

            $saved = $this->cache($stores);

            if (false !== $saved) {
                echo sprintf('Stores saved. Filesize: %d bytes' . PHP_EOL, $saved);
            }
        }

        echo sprintf('%d stores retrieved.' . PHP_EOL, count($stores));

        $result = $this->save($stores, $this->sql);

        if ($result) {
            echo 'Pepperjam Stores imported into database.' . PHP_EOL;
        } else {
            echo 'Pepperjam Stores no new updates into database.' . PHP_EOL;
        }

        if ( ! empty($this->categories)) {
            $this->updateCategories($this->categories, $this->categorySql);
        }

        echo 'Pepperjam Import finished.' . PHP_EOL . PHP_EOL;

    }

    public function load($date)
    {
        $data = @file_get_contents('downloads/pj-stores-' . $date . '.json');

        return (false !== $data) ? json_decode($data) : $data;
    }

    public function getAllStores($url, $query = [])
    {
        $stores = $this->getStores($url, $query);
        $count  = count($stores);
        $total  = $this->meta->pagination->total_results;

        while ($total > $count) {
            $query['query']['page'] += 1;
            array_push($stores, ...$this->getStores($url, $query));
            $count = count($stores);
        }

        echo sprintf('%d stores retrieved. Out of: %d' . PHP_EOL, $count, $total);

        return $stores;
    }

    public function getStores($url, $query = [])
    {
        $res = $this->client->get($url, $query);

        $data = json_decode($res->getBody()); // object

        $this->meta = $data->meta;

        return $data->data;
    }

    public function cache($stores)
    {
        return file_put_contents('downloads/pj-stores-' . date('Y-m-d') . '.json', json_encode($stores));
    }

    public function save($stores, $sql)
    {
        $result = 0;

        R::freeze(true);
        R::begin();
        try {
            foreach ($stores as $store) {
                $result += R::exec($sql, [
                    ':network_id'           => self::AFFILIATE_NETWORK_ID,
                    ':adv_store_id'         => $store->id,
                    ':adv_store_name'       => $store->name,
                    ':adv_store_raw_data'   => json_encode($store),
                    ':adv_store_categories' => $this->getCategories($store),
                    ':store_status'         => ($store->status == 'joined') ? 1 : 0,
                ]);
            }
            R::commit();
        } catch (\Exception $ex) {
            R::rollback();
            echo $ex->getMessage() . PHP_EOL;
            echo $ex->getTraceAsString() . PHP_EOL;
        }

        /*
         * With ON DUPLICATE KEY UPDATE, the affected-rows value per row is 1 if the row is inserted as a new row,
         * 2 if an existing row is updated, and 0 if an existing row is set to its current values.
         */
        echo $result . ' affected rows for cofundstore_import' . PHP_EOL;

        return $result;
    }

    public function getCategories($adv)
    {
        if (isset($adv->category)) {
            $categories = $adv->category;
            $ids        = [];
            foreach ($categories as $category) {
                $this->categories[$category->id] = $category->name;
                $ids[]                           = $category->id;
            }

            return join(',', $ids);
        }

        return;
    }

}