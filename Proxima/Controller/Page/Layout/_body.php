<body id="<?php echo "{$this->controller}-page" ?>">
    
    <div id="page">
        
        <div id="header" class="clearfix">
            <?php include $this->template('_header'); ?>
        </div><!-- end header -->
        
        <div id="content" class="clearfix">
            
            <div id="main">
                <?php echo $this->layout_content; ?>
                <hr />
            </div><!-- end main content -->
            
            <div id="sub">
                <?php include $this->template('_sub'); ?>
                <hr />
            </div><!-- end sub content -->
            
            <div id="local">
                <?php include $this->template('_local'); ?>
            </div><!--  end local nav -->
            
            <div id="nav">
                <?php include $this->template('_nav'); ?>
            </div><!-- end main nav -->
            
        </div><!-- end content -->
        
        <div id="footer" class="clearfix">
            <?php include $this->template('_footer'); ?>
        </div><!-- end footer -->
        
    </div><!-- end page -->
    
    <div id="extra1"><?php include $this->template('_extra1'); ?></div>
    <div id="extra2"><?php include $this->template('_extra2'); ?></div>
    
</body>
