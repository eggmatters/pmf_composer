<?php ?>
<!-- Carousel
    ================================================== -->
<div id="myCarousel" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#myCarousel" data-slide-to="1"></li>
    <li data-target="#myCarousel" data-slide-to="2"></li>
  </ol>
  <div class="carousel-inner" role="listbox">
    <div class="item active">
<!--      <img class="first-slide" src="data:image/gif;base64,R0lGODlhAQABAIAAAHd3dwAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==" alt="First slide">-->
<!--      <img class="first-slide" src="/assets/images/wooden2.jpeg" alt="First slide">-->
      <img class="first-slide" src="https://s3-us-west-2.amazonaws.com/pmf-assets/wooden2.jpeg" alt="First slide">
      <div class="container">
        <div class="carousel-caption">
          <h1>Not Just for Poor People</h1>
          <p>The PMF is a robust, RESTful MVC framework with a significantly less footprint than the other guys. Lower footprint means greater efficiency and faster execution.</p>
          <p><a class="btn btn-lg btn-primary" href="#" role="button">Download today</a></p>
        </div>
      </div>
    </div>
    <div class="item">
      <img class="first-slide" src="https://s3-us-west-2.amazonaws.com/pmf-assets/wooden2.jpeg" alt="First slide">
      <div class="container">
        <div class="carousel-caption">
          <h1>Learn, Create and Innovate</h1>
          <p>This is not just a product. The PMF is also a guide - a learning tool. Use what you learn to create your own or enhance this framework.</p>
          <p><a class="btn btn-lg btn-primary" href="#" role="button">Learn more</a></p>
        </div>
      </div>
    </div>
    <div class="item">
      <img class="first-slide" src="https://s3-us-west-2.amazonaws.com/pmf-assets/wooden2.jpeg" alt="First slide">
      <div class="container">
        <div class="carousel-caption">
          <h1>Putting the Developer back into Development</h1>
          <p>No more hacky arbitrary "best" practices. No more obtuse configurations and unwieldy data layers. You control the code. (mostly ;)</p>
          <p><a class="btn btn-lg btn-primary" href="#" role="button">Browse gallery</a></p>
        </div>
      </div>
    </div>
  </div>
  <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div><!-- /.carousel -->


<!-- Marketing messaging and featurettes
================================================== -->
<!-- Wrap the rest of the page in another container to center all the content. -->

<div class="container marketing">

  <!-- Three columns of text below the carousel -->
  <div class="row">
    <div class="col-lg-4">
      <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIABAADHav///yH+EUNyZWF0ZWQgd2l0aCBHSU1QACwAAAAAAQABAAACAkQBADs=" alt="Generic placeholder image" width="140" height="140">
      <h2>Create</h2>
      <p>Get started quickly. PMF requires minimal setup and less code to serve your site. From controller to model to view, your control flow just got easier.</p>
<!--      <p><a class="btn btn-default" href="#" role="button">View details &raquo;</a></p>-->
    </div><!-- /.col-lg-4 -->
    <div class="col-lg-4">
      <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIABADc8mP///yH5BAEKAAEALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">
      <h2>Innovate</h2>
      <p>Add features to the framework or alter and enhance existing ones. No solution out of the box is right for everybody. This solution does not assume that. 
      Change what you don't like and keep what you do. Share your ideas and they will be adopted into the main line.</p>
    </div><!-- /.col-lg-4 -->
    <div class="col-lg-4">
      <img class="img-circle" src="data:image/gif;base64,R0lGODlhAQABAIABAMc8mP///yH5BAEKAAEALAAAAAABAAEAAAICRAEAOw==" alt="Generic placeholder image" width="140" height="140">
      <h2>Inspire</h2>
      <p>The PMF comes with a full-fledged development tutorials. Not just using this application to develop web applications - but how the PMF itself was developed. 
        Go behind the scenes and connect the dots. Add your special magic and skills to make it better.</p>
    </div><!-- /.col-lg-4 -->
  </div><!-- /.row -->


  <!-- START THE FEATURETTES -->

  <hr class="featurette-divider">

  <div class="row featurette">
    <div class="col-md-7">
      <h2 class="featurette-heading">Declarative Routing <span class="text-muted">No more configs</span></h2>
      <p class="lead">Say goodbye to unwieldy, complex routing configurations. Declarative routing works by matching incoming url's to your existing
        controller methods. Following a formal grammar for url generation, your methods dictate the routing, not the other way around. 
      </p>
    </div>
    <div class="col-md-5">
      <img class="featurette-image img-responsive center-block" src="https://s3-us-west-2.amazonaws.com/pmf-assets/routes.jpeg" alt="Generic placeholder image">
    </div>
  </div>

  <hr class="featurette-divider">

  <div class="row featurette">
    <div class="col-md-7 col-md-push-5">
      <h2 class="featurette-heading">Data Source Abstraction: <span class="text-muted">Database, API, Filesystem, Other . . .</span></h2>
      <p class="lead">Seamlessly integrate data from MySQL, Postgres, NoSQL, API's, CDN's, you name it. You provide the source, PMF does all the rest. 
        Is your data source not supported? You have several options to integrate it, from providing your own normalization, creating your own interface or
        winging it. 
      </p>
    </div>
    <div class="col-md-5 col-md-pull-7">
      <img class="featurette-image img-responsive center-block" src="https://s3-us-west-2.amazonaws.com/pmf-assets/databstract.jpeg"  alt="Generic placeholder image">
    </div>
  </div>

  <hr class="featurette-divider">

  <div class="row featurette">
    <div class="col-md-7">
      <h2 class="featurette-heading">Write More do less</h2>
      <p class="lead">No that's not a typo. Write more effective, usable, functional code. Spend less time combing through application documentation
        and arcane "Best Practices" guidelines. Write code you're familiar with, not domain specific jargon
      </p>
    </div>
    <div class="col-md-5">
      <img class="featurette-image img-responsive center-block" src="https://s3-us-west-2.amazonaws.com/pmf-assets/Galot-Mule-City-300-pulling3.jpg" alt="Generic placeholder image">
    </div>
  </div>

  <hr class="featurette-divider">

  <!-- /END THE FEATURETTES -->


  <!-- FOOTER -->
  <footer>
    <p class="pull-right"><a href="#">Back to top</a></p>
    <p>&copy; 2016 Company, Inc. &middot; <a href="#">Privacy</a> &middot; <a href="#">Terms</a></p>
  </footer>

</div><!-- /.container -->