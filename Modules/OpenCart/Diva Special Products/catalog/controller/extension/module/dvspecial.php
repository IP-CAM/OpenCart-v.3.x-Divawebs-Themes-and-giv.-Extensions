<?php
class ControllerExtensionModuleDvspecial extends Controller
{
    public function index($setting) {
        $this->load->language('diva/module/dvspecial');

        $this->load->model('catalog/product');
        $this->load->model('diva/product');
        $this->load->model('tool/image');
        $this->load->model('diva/rotateimage');

        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
        $this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
        $this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js');
        $this->document->addScript('catalog/view/javascript/diva/countdown/countdown.js');
        if (file_exists('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/diva/module.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->config->get('theme_' . $this->config->get('config_theme') . '_directory') . '/stylesheet/diva/module.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/diva/module.css');
        }

        $data = array();

        /* Catalog Settings */
        $store_id = $this->config->get('config_store_id');

        if(isset($this->config->get('module_dvcontrolpanel_module_price')[$store_id])) {
            $data['show_module_price'] = (int) $this->config->get('module_dvcontrolpanel_module_price')[$store_id];
        } else {
            $data['show_module_price'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_cart')[$store_id])) {
            $data['show_module_cart'] = (int) $this->config->get('module_dvcontrolpanel_module_cart')[$store_id];
        } else {
            $data['show_module_cart'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_wishlist')[$store_id])) {
            $data['show_module_wishlist'] = (int) $this->config->get('module_dvcontrolpanel_module_wishlist')[$store_id];
        } else {
            $data['show_module_wishlist'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_compare')[$store_id])) {
            $data['show_module_compare'] = (int) $this->config->get('module_dvcontrolpanel_module_compare')[$store_id];
        } else {
            $data['show_module_compare'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_hover')[$store_id])) {
            $data['show_module_hover'] = (int) $this->config->get('module_dvcontrolpanel_module_hover')[$store_id];
        } else {
            $data['show_module_hover'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_quickview')[$store_id])) {
            $data['show_module_quickview'] = (int) $this->config->get('module_dvcontrolpanel_module_quickview')[$store_id];
        } else {
            $data['show_module_quickview'] = 0;
        }

        if(isset($this->config->get('module_dvcontrolpanel_module_label')[$store_id])) {
            $data['show_module_label'] = (int) $this->config->get('module_dvcontrolpanel_module_label')[$store_id];
        } else {
            $data['show_module_label'] = 0;
        }

        /* Module Settings */
        if (isset($setting['description']) && $setting['description']) {
            $data['show_description'] = true;
        } else {
            $data['show_description'] = false;
        }

        if (isset($setting['countdown']) && $setting['countdown']) {
            $data['show_countdown'] = true;
        } else {
            $data['show_countdown'] = false;
        }

        if(isset($setting['limit'])) {
            $limit = (int) $setting['limit'];
        } else {
            $limit = 10;
        }

        if (isset($setting['rows'])) {
            $rows = $setting['rows'];
        } else {
            $rows = 1;
        }

        if (isset($setting['items'])) {
            $items = $setting['items'];
        } else {
            $items = 4;
        }

        if (isset($setting['speed'])) {
            $speed = $setting['speed'];
        } else {
            $speed = 500;
        }

        if (isset($setting['auto']) && $setting['auto']) {
            $auto = true;
        } else {
            $auto = false;
        }

        if (isset($setting['navigation']) && $setting['navigation']) {
            $navigation = true;
        } else {
            $navigation = false;
        }

        if (isset($setting['pagination']) && $setting['pagination']) {
            $pagination = true;
        } else {
            $pagination = false;
        }

        $use_hover_image = $data['show_module_hover'];

        $new_results = $this->model_catalog_product->getLatestProducts(10);

        $data['products'] = array();

        if($data['show_countdown']) {
            $results = $this->model_diva_product->getCountdownSpecials($limit);
        } else {
            $filter_data = array(
                'start' => 0,
                'limit' => $limit
            );
            $results = $this->model_catalog_product->getProductSpecials($filter_data);
        }


        foreach ($results as $result) {
            if ($result['image']) {
                $image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
            } else {
                $image = false;
            }

            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                $price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $price = false;
            }

            if ((float)$result['special']) {
                $special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
            } else {
                $special = false;
            }

            if ($this->config->get('config_review_status')) {
                $rating = $result['rating'];
            } else {
                $rating = false;
            }

            if($result['description']) {
                $description = utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..';
            } else {
                $description = false;
            }

            if (isset($result['date_start']) && $result['date_start']) {
                $date_start = $result['date_start'] ;
            } else {
                $date_start = false;
            }

            if(isset($result['date_end']) &&  $result['date_end']) {
                $date_end = $result['date_end'] ;
            } else {
                $date_end = false;
            }

            $is_new = false;
            if ($new_results) {
                foreach($new_results as $new_product) {
                    if($result['product_id'] == $new_product['product_id']) {
                        $is_new = true;
                    }
                }
            }

            if($use_hover_image == 1) {
                $product_id = $result['product_id'];
                $rotate_image = $this->model_diva_rotateimage->getProductRotateImage($product_id);

                if($rotate_image) {
                    $rotate_image = $this->model_tool_image->resize($rotate_image, $setting['width'], $setting['height']);
                } else {
                    $rotate_image = false;
                }
            } else {
                $rotate_image = false;
            }

            $data['products'][] = array(
                'product_id'    => $result['product_id'],
                'thumb'   	    => $image,
                'rotate_image'  => $rotate_image,
                'name'    	    => $result['name'],
                'price'   	    => $price,
                'special' 	    => $special,
                'is_new'        => $is_new,
                'date_start'  	=> $date_start,
                'date_end'    	=> $date_end,
                'rating'        => $rating,
                'description'   => $description,
                'href'    	    => $this->url->link('product/product', 'product_id=' . $result['product_id']),
            );
        }

        $data['slide'] = array(
            'auto'  => $auto,
            'rows'  => $rows,
            'navigation' => $navigation,
            'pagination' => $pagination,
            'speed' => $speed,
            'items' => $items
        );

        return $this->load->view('diva/module/dvspecial', $data);
    }
}