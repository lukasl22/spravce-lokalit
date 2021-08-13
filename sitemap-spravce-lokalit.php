<?php
function create_sitemap()
{
    global $wpdb;
    $tablename_mesta = $wpdb->prefix . 'mesta';
    $tablename_cast = $wpdb->prefix . 'cast';
    $tablename_ulice = $wpdb->prefix . 'ulice';
    $pocet = 0;

    $sql_mesta = $wpdb->prepare("SELECT * FROM `$tablename_mesta`");
    $result_mesta =  $wpdb->get_results($sql_mesta);
    if ($result_mesta > 0) {
        $xml_mesta = new DOMDocument('1.0', 'UTF-8');
        $xml_casti = new DOMDocument('1.0', 'UTF-8');
        $xml_ulice = new DOMDocument('1.0', 'UTF-8');
        $xml_ulice2 = new DOMDocument('1.0', 'UTF-8');
        $xml_ulice3 = new DOMDocument('1.0', 'UTF-8');
        $base_url = get_site_url() . '/internet';
        $xml_mesta->formatOutput = true;
        $xml_casti->formatOutput = true;
        $xml_ulice->formatOutput = true;
        $xml_ulice2->formatOutput = true;
        $xml_ulice3->formatOutput = true;

        $fitness_mesta = $xml_mesta->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $xml_mesta->appendChild($fitness_mesta);
        $fitness_casti = $xml_casti->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $xml_casti->appendChild($fitness_casti);
        $fitness_ulice = $xml_ulice->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $xml_ulice->appendChild($fitness_ulice);
        $fitness_ulice2 = $xml_ulice2->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $xml_ulice2->appendChild($fitness_ulice2);
        $fitness_ulice3 = $xml_ulice3->createElementNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'urlset');
        $xml_ulice3->appendChild($fitness_ulice3);

        foreach ($result_mesta as $print_mesta) :
            $mesta_result = $print_mesta->town;
            $id = $print_mesta->id;
            $mesta = sanitize_title($mesta_result);
            $user_mesta = $xml_mesta->createElement("url");
            $fitness_mesta->appendChild($user_mesta);
            $uid = $xml_mesta->createElement("loc",  $base_url . '/' . $mesta);
            $user_mesta->appendChild($uid);
            $changefreq_mesta = $xml_mesta->createElement("changefreq", "daily");
            $user_mesta->appendChild($changefreq_mesta);


            $sql_cast = "SELECT * FROM `$tablename_cast` WHERE id_town= %d";
            $sql_cast = $wpdb->prepare($sql_cast, $id);
            $result_cast = $wpdb->get_results($sql_cast);
            foreach ($result_cast as $print_cast) :
                $cast_result = $print_cast->cast;
                $id_cast = $print_cast->id;
                $cast = sanitize_title($cast_result);
                $user_casti = $xml_casti->createElement("url");
                $fitness_casti->appendChild($user_casti);
                $uid = $xml_casti->createElement("loc",  $base_url . '/' . $mesta . '/' . $cast);
                $user_casti->appendChild($uid);
                $changefreq_casti = $xml_casti->createElement("changefreq", "daily");
                $user_casti->appendChild($changefreq_casti);


                $sql_ulice = "SELECT * FROM `$tablename_ulice` WHERE id_cast= %d";
                $sql_ulice = $wpdb->prepare($sql_ulice, $id_cast);
                $result_ulice = $wpdb->get_results($sql_ulice);
                foreach ($result_ulice as $print_ulice) :
                    $ulice_result = $print_ulice->ulice;
                    $id_ulice = $print_ulice->id;
                    $ulice = sanitize_title($ulice_result);
                    $pocet++;
                    if ($pocet < 30000) :
                        $user_ulice = $xml_ulice->createElement("url");
                        $fitness_ulice->appendChild($user_ulice);
                        $uid = $xml_ulice->createElement("loc",  $base_url . '/' . $mesta . '/' . $cast . '/' . $ulice);
                        $user_ulice->appendChild($uid);
                        $changefreq_ulice = $xml_ulice->createElement("changefreq", "daily");
                        $user_ulice->appendChild($changefreq_ulice);
                    elseif ($pocet >= 30000 && $pocet < 60000) :
                        $user_ulice2 = $xml_ulice2->createElement("url");
                        $fitness_ulice2->appendChild($user_ulice2);
                        $uid = $xml_ulice2->createElement("loc",  $base_url . '/' . $mesta . '/' . $cast . '/' . $ulice);
                        $user_ulice2->appendChild($uid);
                        $changefreq_ulice2 = $xml_ulice2->createElement("changefreq", "daily");
                        $user_ulice2->appendChild($changefreq_ulice2);
                    else :
                        $user_ulice3 = $xml_ulice3->createElement("url");
                        $fitness_ulice3->appendChild($user_ulice3);
                        $uid = $xml_ulice3->createElement("loc",  $base_url . '/' . $mesta . '/' . $cast . '/' . $ulice);
                        $user_ulice3->appendChild($uid);
                        $changefreq_ulice3 = $xml_ulice3->createElement("changefreq", "daily");
                        $user_ulice3->appendChild($changefreq_ulice3);
                    endif;
                endforeach;
            endforeach;
        endforeach;
        $xml_mesta->save(WP_PLUGIN_DIR . "/spravce-lokalit/lokality_mesta.xml");
        $xml_casti->save(WP_PLUGIN_DIR . "/spravce-lokalit/lokality_casti.xml");
        $xml_ulice->save(WP_PLUGIN_DIR . "/spravce-lokalit/lokality_ulice.xml");
        $xml_ulice2->save(WP_PLUGIN_DIR . "/spravce-lokalit/lokality_ulice2.xml");
        $xml_ulice3->save(WP_PLUGIN_DIR . "/spravce-lokalit/lokality_ulice3.xml");
    } else {
        echo "error";
    }
}
