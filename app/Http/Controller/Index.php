<?php
/**
 * Created by PhpStorm.
 * User: i
 * Date: 2019/4/21
 * Time: 23:01
 */

namespace App\Http\Controller;

use App\Pool\Redis\Pool;
use App\Pool\Redis\Redis;

class Index extends Base
{
    public function index()
    {

        $key = $this->request()->getRequestParam('key');
        if (!$key || !in_array($key, [ '1','2', '4', '5'])) {
            $this->toJson(['code' => 1, 'key 不能为空。']);
            return;
        }
        $one = Pool::invoke(function (Redis $redis) use ($key) {
            $k = 'campus:' . $key;
            return $redis->hGetAll($k);
        });

        if (!$one) {
            $this->toJson([
                'code' => 2,
                'message' => '尚未收到教室状态',
            ]);
            return;
        }

        $raw = $one['raw'];
        $result = [];
        $len = strlen($raw);
        $num = intval($len / 4);
        for ($i = 0; $i < $num; $i++) {
            $result[] = $this->parseDatum(substr($raw, $i * 4, 4));
        }

        $this->toJson([
            'code' => 0,
            'data' => $result,
            'meta' => [
                'count' => count($result),
                'timestamp' => $one['timestamp'],
            ]
        ]);
    }

    protected function parseDatum($raw)
    {
        $id = unpack('vId', substr($raw, 0, 2))['Id'];
        $byte2 = unpack('CB', substr($raw, 2, 1))['B'];
        $byte3 = unpack('CB', substr($raw, 3, 1))['B'];
        return [
            'id' => $id,
            'program_source' => $byte2 & 0x03, // 00000011
            'door' => ($byte2 & 0x04) >> 2, // 00000100
            'projector_alarm' => ($byte2 & 0x08) >> 3, // 00001000
            'amplifier' => ($byte2 & 0x10) >> 4, // 00010000
            'screen' => ($byte2 & 0x20) >> 5, // 00100000
            'projector_status' => ($byte2 & 0x40) >> 6, // 01000000
            'classroom_status' => ($byte2 & 0x80) >> 7, // 10000000
            'main_volume' => ($byte3 & 0xf0) >> 4, // 11110000
            'microphone_volume' => $byte3 & 0x0f, // 00001111
        ];
    }
}