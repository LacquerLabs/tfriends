<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <link rel="shortcut icon" href="<?=$this->_base_url;?>assets/favicon.ico" />
        
        <title></title>
                
        <!-- Custom styles for this template -->
        <link href="<?=$this->_base_url;?>assets/theme.css" rel="stylesheet" />

        <!-- Fonts from Google Fonts -->
        <link href="http://fonts.googleapis.com/css?family=Arimo" rel="stylesheet" type="text/css" />
       
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    
    <body role="document">
         <header>
             <br/>
         </header>
         
        <!-- Begin Body -->
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="navbar-container nav-content">
                        <div class="pseudo-row hidden-sm hidden-xs" >
                            <a href="#" class="thumbnail">
                                <img class="img-responsive" data-src="holder.js/220x220" alt="..." />
                            </a>
                        </div>
                        <div class="pseudo-row">
                            <ul class="nav nav-pills nav-stacked">
                                <li class="<?=$this->determine_active('home',$currentMenu);?>">
                                    <a href="#">
                                        <span class="badge pull-right">42</span>
                                        Home
                                    </a>
                                </li>
                                <li class="<?=$this->determine_active('profile',$currentMenu);?>">
                                    <a href="#">Profile</a>
                                </li>
                                <li class="<?=$this->determine_active('messages',$currentMenu);?>">
                                    <a href="#">
                                        <span class="badge pull-right">3</span>
                                        Messages
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-9 page-content">
                    <?php if(isset($this->_breadcrumbs) && count($this->_breadcrumbs)>0) : ?>
                    <div class="pseudo-row">
                        <ol class="breadcrumb">
                            <?php foreach($this->_breadcrumbs as $crumb) : ?>
                            <li class="<?=$crumb['active'];?>" >
                                <?php if($crumb['url']!='') : ?>
                                <a href="<?=$crumb['url'];?>">
                                <?php endif;?>
                                <?=$crumb['what'];?>
                                <?php if($crumb['url']!='') : ?>
                                </a>
                                <?php endif;?>
                            </li>
                            <?php endforeach; ?>
                        </ol>
                    </div>
                    <?php endif; ?>
                    <div class="pseudo-row">
                        <!-- begin page content -->
