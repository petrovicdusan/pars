<?php
if(!class_exists('element_gva_portfolio_carousel')):
   class element_gva_portfolio_carousel{
      public function render_form(){
         $fields = array(
            'type' => 'gva_portfolio_carousel',
            'title' => t('Portfolio Box Carousel'),
            'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title For Admin'),
                  'default'   => t('Portfolio Box Carousel'),
                  'admin'     => true,
                  'class'     => 'display-admin'
               ),
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => t('Animation'),
                  'desc'      => t('Entrance animation for element'),
                  'options'   => gavias_content_builder_animate(),
                  'class'     => 'width-1-2'
               ),
               array(
                  'id'        => 'animate_delay',
                  'type'      => 'select',
                  'title'     => t('Animation Delay'),
                  'options'   => gavias_content_builder_delay_wow(),
                  'desc'      => '0 = default',
                  'class'     => 'width-1-2'
               ),
               array(
                  'id'        => 'el_class',
                  'type'      => 'text',
                  'title'     => t('Extra class name'),
                  'desc'      => t('Style particular content element differently - add a class name and refer to it in custom CSS.'),
               ),
            ),
         );

         gavias_carousel_fields_settings($fields);

         for($i = 1; $i <= 4; $i++){
            $fields['fields'][] = array(
               'id'     => "info_${i}",
               'type'   => 'info',
               'desc'   => "Information for item {$i}"
            );
            $fields['fields'][] = array(
               'id'        => "title_{$i}",
               'type'      => 'text',
               'title'     => t("Title {$i}"),
               'default'   => 'Discover, Explore the Product'
            );
            $fields['fields'][] = array(
               'id'           => "image_{$i}",
               'type'         => 'text',
               'title'        => t("Image {$i}"),
            );
            $fields['fields'][] = array(
               'id'           => "desc_{$i}",
               'type'         => 'text',
               'title'        => t("Description {$i}"),
               'default'      => 'Discover, Explore & understanding the product'
            );
            $fields['fields'][] = array(
               'id'        => "link_{$i}",
               'type'      => 'text',
               'title'     => t("Link {$i}")
            );
           $fields['fields'][] = array(
             'id'        => "target_{$i}",
             'type'      => 'select',
             'title'     => t('Open in new window'),
             'options'   => array( 'off' => 'Off', 'on' => 'On' ),
             'desc'      => t('Adds a target="_blank" attribute to the link.'),
           );
         }

         for($i = 5; $i <= 10; $i++){
            $fields['fields'][] = array(
               'id'     => "info_${i}",
               'type'   => 'info',
               'desc'   => "Information for item {$i}"
            );
            $fields['fields'][] = array(
               'id'        => "title_{$i}",
               'type'      => 'text',
               'title'     => t("Title {$i}")
            );
            $fields['fields'][] = array(
               'id'           => "image_{$i}",
               'type'         => 'text',
               'title'        => t("Image {$i}"),
            );
            $fields['fields'][] = array(
               'id'           => "desc_{$i}",
               'type'         => 'text',
               'title'        => t("Description {$i}"),
            );
            $fields['fields'][] = array(
               'id'        => "link_{$i}",
               'type'      => 'text',
               'title'     => t("Link {$i}")
            );
           $fields['fields'][] = array(
             'id'        => "target_{$i}",
             'type'      => 'select',
             'title'     => t('Open in new window'),
             'options'   => array( 'off' => 'Off', 'on' => 'On' ),
             'desc'      => t('Adds a target="_blank" attribute to the link.'),
           );
         }
         return $fields;
      }

      public static function render_content( $attr = array(), $content = '' ){
         global $base_url;
         $default = array(
            'title'           => '',
            'more_link'       => '',
            'more_text'       => 'View all services',
            'el_class'        => '',
            'animate'         => '',
            'animate_delay'   => '',
            'col_lg'          => '4',
            'col_md'          => '3',
            'col_sm'          => '2',
            'col_xs'          => '1',
            'auto_play'       => '0',
            'pagination'      => '0',
            'navigation'      => '0'
         );

         for($i=1; $i<=10; $i++){
            $default["title_{$i}"] = '';
            $default["image_{$i}"] = '';
            $default["desc_{$i}"] = '';
            $default["link_{$i}"] = '';
            $default["target_{$i}"] = '';
         }

         extract(gavias_merge_atts($default, $attr));

         $_id = gavias_content_builder_makeid();
         if($animate) $el_class .= ' wow ' . $animate;
         ob_start();
         ?>
         <div class="gsc-service-carousel <?php echo $el_class ?>" <?php print gavias_content_builder_print_animate_wow('', $animate_delay) ?>>
            <div class="no-padding owl-carousel init-carousel-owl owl-loaded owl-drag" data-items="<?php print $col_lg ?>" data-items_lg="<?php print $col_lg ?>" data-items_md="<?php print $col_md ?>" data-items_sm="<?php print $col_sm ?>" data-items_xs="<?php print $col_xs ?>" data-loop="1" data-speed="500" data-auto_play="<?php print $auto_play ?>" data-auto_play_speed="2000" data-auto_play_timeout="5000" data-auto_play_hover="1" data-navigation="<?php print $navigation ?>" data-rewind_nav="0" data-pagination="<?php print $pagination ?>" data-mouse_drag="1" data-touch_drag="1">
               <?php for($i=1; $i<=10; $i++){ ?>
                  <?php
                     $title = "title_{$i}";
                     $image = "image_{$i}";
                     $desc = "desc_{$i}";
                     $link = "link_{$i}";
                     $target = "target_{$i}";
                  ?>
                  <?php if($$title && $$image) { ?>
                   <div class="item">
                     <div>
                       <div class="portfolio-v1">
                         <div class="portfolio-content">
                           <?php if($$link) { ?>
                              <a class="link" href="<?php echo $$link ?>" <?php if($$target == 'on') { ?> target="_blank" <?php } ?> >
                           <?php } ?>

                              <div class="portfolio-images">
                                <div class="gallery-popup">
                                  <div class="item-image">
                                    <img src="<?php print $$image ?>" alt="" loading="lazy" typeof="foaf:Image" />
                                  </div>
                                </div>
                              </div>
                              <div class="content-inner">
                                <div class="portfolio-information">
                                  <h2 class="title">
                                    <span><?php print $$title ?></span>
                                  </h2>
                                  <div class="portfolio-hover">
                                    <?php if($$desc){ ?>
                                      <div class="desc">
                                        <div class="field field--name-body field--type-text-with-summary field--label-hidden field__item"><p><?php print $$desc ?></p></div>
                                      </div>
                                    <?php } ?>
                                  </div>
                                </div>
                              </div>

                           <?php if($$link){ ?>
                             </a>
                           <?php } ?>

                         </div>
                       </div>
                     </div>
                   </div>

                  <?php } ?>
               <?php } ?>
            </div>
         </div>

         <?php return ob_get_clean();
      }

   }
 endif;



