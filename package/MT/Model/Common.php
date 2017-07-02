<?php
/**
 * Created by PhpStorm.
 * User: chiennn
 * Date: 02/07/2017
 * Time: 08:37
 */

namespace MT\Model;
use MT;
use MT\Model;


class Common
{
    const KEY_TOTAL_DAILY_DOWNLOAD = 'total:daily:download';
    const KEY_TOTAL_DAILY_UPLOAD = 'total:daily:upload';

    public static function getConfigGoogle(){
        $arr_config = [
            //dev-game
            [
                'key' => 'AIzaSyBlTq9Ah3zwzATAT0C76Fq7wVz0aE6wazM',
                'client_id' => '305277173466-i7u7cmv0a7gqco2rj86a9p99jbokp9lq.apps.googleusercontent.com',
                'client_secret' => 'yuNS6kJUsU69NX7rPXRIrU4C',
                'refresh_token' => '1/yeOm41z-ONX4kdpghOUqprx_t3dCOGY9bNIiuG_HipLOvis2gCBMiGdKa1FHkWzL'
            ],
            //dev-videos 1
            [
                'key' => 'AIzaSyDdESZMVy2EVOYp9bAv-ITHmp-bL8k_duY',
                'client_id' => '169282978726-400i9lqenmcsoiffkpapo7egff3ftg2h.apps.googleusercontent.com',
                'client_secret' => '-7BNy9BHAQhjbbdbUxY0_F9N',
                'refresh_token' => '1/NCAoip3uBvfKdllnFGQsmoA_bhUMPMmptrjupkCxaOc'
            ],
            //dev-videos 2
            [
                'key' => 'AIzaSyAEy-qHsNqqqbMWxk8aIBWh9aKkR8Nn5mc',
                'client_id' => '1096053753081-vi34b9hahb3vkt02710tgsmii5l79l3i.apps.googleusercontent.com',
                'client_secret' => 'X4S521QE2wE5UYg5_NPfbiqc',
                'refresh_token' => '1/zFjGl5eP2y8EGVghFccmWRQAk_FxdqNFw7fj3tf5fUo'
            ],
            //dev-videos 3
            [
                'key' => 'AIzaSyBJWr4u9UbeOKjnxQlZLe5JU4NScUZ9VHE',
                'client_id' => '399129151516-drgguh0e4se1lvu8h81uil91rmsu0fcv.apps.googleusercontent.com',
                'client_secret' => 'm6etjZvXpuCe-UDCY2ODwCBI',
                'refresh_token' => '1/N24vwhQIYQr7tgsR1anCN8DSIEetIXQlZSuKoSjFPj7qpPPLDO_JiMkPGJ_Wa527'
            ],
            //dev-videos 4
            [
                'key' => 'AIzaSyBNAGAak82wm1cqJicnxi8HMw5_DKO434Y',
                'client_id' => '1027551718916-p6dn8l48ho053orfclvsqgvng6mv6p2d.apps.googleusercontent.com',
                'client_secret' => '3b0cEUg3CysynnifDC3BZr8Y',
                'refresh_token' => '1/Y_XiaLHBB3Gbd5_i6_iHgWdpMOIPg9LuBf6IcePu23w'
            ],
            //dev-videos
            [
                'key' => 'AIzaSyAWvatPLULUTpOBOOhJlrTGuRYhu4jf6Mk',
                'client_id' => '1067824536040-e0dc0lecpltdltopt15qusdbiiqg6994.apps.googleusercontent.com',
                'client_secret' => 'ux7pIQOxCCpqRnGb9_RUWD48',
                'refresh_token' => '1/UJ2NhGdYeci-zMap69Br1bUfxc-Lm66IBHrZ4af1RZla7sE-FGx9w320cShVi5EP'
            ],

            //dev-videos 6 -- ng
            [
                'key' => 'AIzaSyAHaTsBHC5AgJ8cHyyJV9cjkNfgow_k3uQ',
                'client_id' => '32320109373-8oo0spnfblegtper561lr3hngkfclbs6.apps.googleusercontent.com',
                'client_secret' => 'SPVyaTEsTFBohjEl6G-gVe5T',
                'refresh_token' => 'ya29.Glt7BAr28_vJ1jNwSsQ5zIMU3zfEr8vskeRIhFbWF3F7X1gMV3z_Nps10-PfWlCxinWDHyg9x53fF4iPd5h_5yeI2MVo3DY-qPuqcRgVyfpE2_xLelcxrgC9sG0w'
            ],
            //dev-videos 7 -- ng-2
            [
                'key' => 'AIzaSyCtLlkcdzmlmsH8Q3_sd9FTONcEoI4Jdp4',
                'client_id' => '674162481450-6f310dc20lpnbvksuv1roqlt6kpesle4.apps.googleusercontent.com',
                'client_secret' => 'LG2fgtrzhwUasMB2-eVpOAbY',
                'refresh_token' => 'ya29.Glt7BCJGE2Osu6nu2VfT4XA9G9dTJdub7xFmgnL7G8fRyp7JJhRVhr1m0OL-b4wsmDPCsTdRuLOnEorNAwh3W_v6Inxa999ZTC5Cc27mU3lCh8d4_FbQvLCs-3Rm'
            ],
            //dev-videos 8 -ng3
            [
                'key' => 'AIzaSyDHiTQ0wi3-2tPmGc-Wfn6Q9SgnZSiOLdA',
                'client_id' => '253100134519-t02mgo9hattt4cbgf99gg7866sfr4680.apps.googleusercontent.com',
                'client_secret' => 'OKrm82cMiHkODq7J-3nhOK-Y',
                'refresh_token' => 'ya29.Glt7BFxnnUsD8-PAmiWMpjD9L-XzNkg0o7RKrYXWKl0sVUQKtlFS9V2AvNM63HzibAZRjo5TOvesJFH9CMj_9biO5orCFW5N9l8r3T6Sj9P-sC_-t4A-rOP1Y-Zm'
            ],
            //dev-videos 9 -ng4
            [
                'key' => 'AIzaSyCWOgDp_kJ2O_WQbVXtKYECTPfTdirt5jY',
                'client_id' => '394072495620-54fa50gdm3rd56mi75afrcpe07g2a8s6.apps.googleusercontent.com',
                'client_secret' => 'lL8tN_nJtZjoFQxmgGIKULFN',
                'refresh_token' => 'ya29.Glt7BDOityK-_BrZA0I-MwBexF5ABT0D9JmpHvgycinu22WoGh8OLt8BBWYRLYZ7T5n5fvZTmdGLyw79gYrQNOVfx5AwkMXKNbuTG_FeTMHCFo8Qkp2nYiiycJdJ'
            ],
            //dev-videos 10 -ng5
            [
                'key' => 'AIzaSyCLfNQDSveRj74mcuQrMjO5Gtk29VpfKCQ',
                'client_id' => '577734619926-imoos0820titmr3rurt67ao53e56cosa.apps.googleusercontent.com',
                'client_secret' => '_bw-c8p0pwurjbRXvjbsr_WJ',
                'refresh_token' => 'ya29.Glt7BEQPaIG6L3XGZ_nXSW6iQpDqKWbvV1KhV_hC8uij2DoYKk38U9-gBGlk6fnBL53Azn3iGUEjwwh0aY8q--jGXwHoFVo5Grh-bkDzAPv-fL1bRhFKLAKw5xSr'
            ],
            //dev-videos 11 -ng6
            [
                'key' => 'AIzaSyDqhWGKsnf4PH51g7AKTOd9e7qIHtQHPxw',
                'client_id' => '27324382118-tae8rh3ln0l91pvfo79j7igeuolmlirq.apps.googleusercontent.com',
                'client_secret' => 'aIFkfoJyns1LkO_B74f5ktYU',
                'refresh_token' => 'ya29.Glt7BGvw4z0plIJ3HHKMeyJ_FK5MZu3aO4-Pp_DrHSq7s2fDUMmQwodib_8sFDYZFaCRksF_kNPdyBxJXmEosY1VLqJXZ87bc2UCZK05-wkkwFMT_mf86ogFU-9D'
            ]
        ];

        return $arr_config;
    }

    public static function changeConfigApi(){

        $redis = MT\Nosql\Redis::getInstance('caching');
        $total_daily = $redis->GET(self::KEY_TOTAL_DAILY_UPLOAD);

        if(empty($total_daily)){
            $total_daily = 0;
        }

        $key_config = floor($total_daily/100);

        return self::getConfigGoogle()[$key_config];
    }
}