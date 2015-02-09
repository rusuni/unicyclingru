<?php
//  @copyright	Copyright (C) 2013 IceTheme. All Rights Reserved
//  @license	Copyrighted Commercial Software 
//  @author     IceTheme (icetheme.com)

defined('_JEXEC') or die;

// Include Variables
include_once(JPATH_ROOT . "/templates/" . $this->template . '/icetools/vars.php'); 

if ((JRequest::getCmd("tmpl", "index") != "offline") && (JRequest::getCmd("tmpl", "index") != "soon") && ($it_comingsoon == 0)) { ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $this->language; ?>" lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
<head>

<?php if ($it_responsive == 1) { ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php } ?>

<jdoc:include type="head" />

	<?php
    // Include CSS and JS variables 
    include_once(IT_THEME_DIR.'/icetools/css.php');
    ?>
   
</head>

<body class="<?php echo $pageclass->get('pageclass_sfx'); ?>">   

    <header id="header">
        
        <div class="container">
            
            <?php if ($it_logo != "") { ?>
            <div id="logo">	
                <a href="<?php echo $this->baseurl ?>"><?php echo $it_logo_img; ?></a> 	
            </div>
            <?php } ?> 
            
            <?php if ($it_language != 0) { ?>
            <div id="language">	
               <jdoc:include type="modules" name="language" />
            </div> 
            <?php } ?> 
            
             <?php if ($it_topmenu != 0) { ?>
            <div id="topmenu">
              <jdoc:include type="modules" name="topmenu" />
            </div>
            <?php } ?>
        
           
             <?php if ($it_search != 0) { ?>
            <div id="search">
                <jdoc:include type="modules" name="search" />
            </div>
            <?php } ?>    
       
            <?php if ($it_mainmenu != 0) { ?>
            <div id="mainmenu">
                <jdoc:include type="modules" name="mainmenu" />
            </div>
            <?php } ?>  
            
            <jdoc:include type="modules" name="breadcrumbs" />
            
            <?php if ($it_iceslideshow != 0) { ?>
                <div id="iceslideshow" >
                    <jdoc:include type="modules" name="iceslideshow" />
                </div>
            <?php } ?>
            
            <?php if ($it_promo != 0) { ?> 
            <div id="promo" class="row" >
                <jdoc:include type="modules" name="promo" style="promo" />
            </div>
            <?php } ?>
            
        </div>
    
    </header>
    
	
    <!-- content -->
    <section id="content">
    
    	<div class="container">

          
            <div class="row">
            
                <!-- Middle Col -->
                <div id="middlecol" class="<?php echo $content_span;?>">
                
                    <div class="inside">
                                               
                        <jdoc:include type="message" />
                    
                        <jdoc:include type="component" />
                    
                    </div>
                
                </div><!-- / Middle Col  -->
                    
                <?php if ($it_sidebar != 0) { ?> 
                <!-- sidebar -->
                <div id="sidebar" class="<?php echo $sidebar_span;?> <?php if($it_sidebar_pos == 'right') {  echo 'sidebar_right'; } ?>" >
                    <div class="inside">    
                        <jdoc:include type="modules" name="sidebar" style="sidebar" />
                    </div>
                </div><!-- /sidebar -->
                <?php } ?> 
                               
            </div>
              
			<?php if ($it_icecarousel != 0) { ?>
            <div id="icecarousel">
                <div class="container">
                    <jdoc:include type="modules" name="icecarousel" style="slider" />      
                </div>     
            </div>   
            <?php } ?>

    	</div>
            
    </section><!-- /content  --> 
  
      
	<footer id="footer" >
    
    	<div class="container">
		
			<?php if ($it_footer != 0) { ?>
            <div class="row" >
                <jdoc:include type="modules" name="footer" style="footer" />
            </div>
            <?php } ?>
            
            <?php if ($it_footermenu != 0) { ?>
                <div class="footermenu">
                    <jdoc:include type="modules" name="footermenu" />
                </div>
            <?php } ?> 
            
        
            <div id="copyright">
                <p class="copytext">
                    &copy; <?php echo date('Y');?> <?php echo $sitename; ?> 
                </p>          
                <jdoc:include type="modules" name="copyrightmenu" />
            </div>
            
			<?php if ($it_social == 1) {  
             // Include slide module position
       		 include_once(IT_THEME_DIR.'/icetools/social_icons.php');
            } ?> 
            
        </div>
        
	</footer>   
  
	<?php if ($it_gotop != 0) { ?>
    <div id="gotop" class="">
        <a href="#" class="scrollup"><?php echo JText::_('TPL_FIELD_SCROLL'); ?></a>
    </div>
    <?php } ?>  
 

</body>
</html>
<?php } ?>