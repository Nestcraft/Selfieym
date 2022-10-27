<link href="/public/css/custom.css" rel="stylesheet"/>
<style>

 .topspace{
  margin-top: 100px;
}

.influencer-intro2 {
  background: rgb(183,218,223);
  background: linear-gradient(90deg, rgba(183,218,223,1) 0%, rgba(241,226,209,1) 100%);
}

.influencer-intro {background: rgb(156,209,212);
  background: linear-gradient(90deg, rgba(156,209,212,1) 0%, rgba(196,221,216,1) 100%);}

  .search-bar1{
    background-color:#cccccc; padding-top:10px;
  }

  .lgtitle { font-size:46px; font-family: "Roboto Condensed", Helvetica, Arial, sans-serif;  font-weight:thin;
  line-height: 1.4;color:#1d9066;
}
.stl {font-family:helvetica neue,Helvetica,Arial,sans-serif; font-weight:thin;
}

.bottomfooter {
  background: #f3f1f1;
  padding: 12px 10px;
  padding-bottom: 0px;
}
.review{color:#5fb48d; font-size1:16px;}

.profile-featured-tag {
  position: absolute;
  top: 14px;
  right: 20px;
}

</style>
@extends('layouts.master')

<
   




<section class="intro-inner1 jumbotron jumbotron-fluid text-left influencer-intro">
  <div class="container mt-5">
    <h1 class="lgtitle">Find & Hire the best <i><b>Influencer</b></i> for your brand marketing</h1>
    <p class="lead text-muted">Coonect with of our amazing influencers from around the world on our secure,
    flexible and cost-effective platform. Start your next social media marketing campaign with 20,000+ Influencers, Youtubers & Bloggers</p>
    <p>

      <div class="row">
        <div class="col-md-3 mb-3">
          <select class="custom-select d-block w-100" id="category" required="">
            <option value="">Select Category...</option>
            <option>Finace</option>
          </select>
          <div class="invalid-feedback">
            Please select a valid category.
          </div>
        </div>
        <div class="col-md-2 mb-3">

         <select class="custom-select d-block w-100" id="category" required="">
          <option value="">Social Platform</option>
          <option>Any</option>
          <option>Instagram</option>
          <option>facebook</option>
          <option>Youtube</option>
          <option>Twitter</option>
          <option>Tiktok</option>
          <option>Blogs/Website</option>

        </select>

      </div>
      <div class="col-md-2 mb-3">

       <div class="input-group mb-3">
        <input type="text" class="form-control" placeholder="Enter Followers" aria-label="Minimum Followers" aria-describedby="basic-addon2">
        <div class="input-group-append">
          <span class="input-group-text" id="basic-addon2">k</span>
        </div>
      </div>

    </div>

    <div class="col-md-3 mb-3">
      <input type="text" class="form-control" id="keyword" placeholder="Search anything..." required="">
    </div>
    <div class="col-md-2 mb-3">
      <a href="#" class="btn btn-primary">    <i class="fa fa-search"></i> Search</a>


    </div>
  </div>
</div>

</section>
<div class="container">
 <div class="row">
  <div class="col-md-3 page-sidebar mobile-filter-sidebar pb-4">
    <aside>
      <div class="inner-box enable-long-words">

       <h5 class="list-title"><b>Side widget</b></h5>
       <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's 
       standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. </p>
       <h5 class="list-title"><b>Side widget</b></h5>
       <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's 
       standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. </p>
       <h5 class="list-title"><b>Side widget</b></h5>
       <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's 
       standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. </p>
       <h5 class="list-title"><b>Side widget</b></h5>
       <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's 
       standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. </p>

     </div>
   </aside>
 </div>

 <div class="col-md-9">
  <div class="">
    <div class="container">

      <div class="row">
        
        <div class="col-md-4">
         <div class="card mb-4 box-shadow">
           <img class="card-img-top" img src="/public/images/profile_images/man1.jpg" alt="Influencer name & Title">
           <div class="card-body">
            <h3>Harsh Roy</h3>
            <ul class="list-unstyled list-inline rating mb-0 review">
              <li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>
              <li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>
              <li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>
              <li class="list-inline-item mr-0"><i class="fas fa-star amber-text"></i></li>
              <li class="list-inline-item"><i class="fas fa-star-half-alt amber-text"></i></li>
              <li class="list-inline-item"><p class="text-muted">4.5 (6 Reviews)</p></li>
            </ul>
            <a href="https://selfieym.com/tag/finance" class="label badge-dark">Marketing</a> <span><a href="https://selfieym.com/tag/finance" class="label badge-dark">Design</a></span>

            <i class="fa fa-map-marker" aria-hidden="true"></i> Delhi

          </div>
          <div class="bottomfooter">
           <p><b>STARTING AT:</b> <span class="badge badge-success"> ₹ 900</span> 
            <span class="fa-pull-right"><i class="fa fa-users" aria-hidden="true"></i> <b>19.7k</b> </span></p>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<!--Carousel Wrapper-->
    <div id="multi-item-example" class="carousel slide carousel-multi-item" data-ride="carousel">

      <!--Controls-->
      <div class="controls-top">
        <a class="btn-floating" href="#multi-item-example" data-slide="prev"><i class="fa fa-chevron-left"></i></a>
        <a class="btn-floating" href="#multi-item-example" data-slide="next"><i class="fa fa-chevron-right"></i></a>
      </div>
      <!--/.Controls-->

      <!--Indicators-->
      <ol class="carousel-indicators">
        <li data-target="#multi-item-example" data-slide-to="0" class="active"></li>
        <li data-target="#multi-item-example" data-slide-to="1"></li>
        <li data-target="#multi-item-example" data-slide-to="2"></li>
      </ol>
      <!--/.Indicators-->

      <!--Slides-->
      <div class="carousel-inner" role="listbox">

        <!--First slide-->
        <div class="carousel-item active">

          <div class="row">
            <div class="col-md-4">
              <div class="card mb-2">
                <img class="card-img-top" src="https://mdbootstrap.com/img/Photos/Horizontal/Nature/4-col/img%20(34).jpg"
                  alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">Card title</h4>
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's content.</p>
                  <a class="btn btn-primary">Button</a>
                </div>
              </div>
            </div>

            <div class="col-md-4 clearfix d-none d-md-block">
              <div class="card mb-2">
                <img class="card-img-top" src="https://mdbootstrap.com/img/Photos/Horizontal/Nature/4-col/img%20(18).jpg"
                  alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">Card title</h4>
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's content.</p>
                  <a class="btn btn-primary">Button</a>
                </div>
              </div>
            </div>

            <div class="col-md-4 clearfix d-none d-md-block">
              <div class="card mb-2">
                <img class="card-img-top" src="https://mdbootstrap.com/img/Photos/Horizontal/Nature/4-col/img%20(35).jpg"
                  alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">Card title</h4>
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's content.</p>
                  <a class="btn btn-primary">Button</a>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!--/.First slide-->

        <!--Second slide-->
        <div class="carousel-item">

          <div class="row">
            <div class="col-md-4">
              <div class="card mb-2">
                <img class="card-img-top" src="https://mdbootstrap.com/img/Photos/Horizontal/City/4-col/img%20(60).jpg"
                  alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">Card title</h4>
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's content.</p>
                  <a class="btn btn-primary">Button</a>
                </div>
              </div>
            </div>

            <div class="col-md-4 clearfix d-none d-md-block">
              <div class="card mb-2">
                <img class="card-img-top" src="https://mdbootstrap.com/img/Photos/Horizontal/City/4-col/img%20(47).jpg"
                  alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">Card title</h4>
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's content.</p>
                  <a class="btn btn-primary">Button</a>
                </div>
              </div>
            </div>

            <div class="col-md-4 clearfix d-none d-md-block">
              <div class="card mb-2">
                <img class="card-img-top" src="https://mdbootstrap.com/img/Photos/Horizontal/City/4-col/img%20(48).jpg"
                  alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">Card title</h4>
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's content.</p>
                  <a class="btn btn-primary">Button</a>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!--/.Second slide-->

        <!--Third slide-->
        <div class="carousel-item">

          <div class="row">
            <div class="col-md-4">
              <div class="card mb-2">
                <img class="card-img-top" src="https://mdbootstrap.com/img/Photos/Horizontal/Food/4-col/img%20(53).jpg"
                  alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">Card title</h4>
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's content.</p>
                  <a class="btn btn-primary">Button</a>
                </div>
              </div>
            </div>

            <div class="col-md-4 clearfix d-none d-md-block">
              <div class="card mb-2">
                <img class="card-img-top" src="https://mdbootstrap.com/img/Photos/Horizontal/Food/4-col/img%20(45).jpg"
                  alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">Card title</h4>
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's content.</p>
                  <a class="btn btn-primary">Button</a>
                </div>
              </div>
            </div>

            <div class="col-md-4 clearfix d-none d-md-block">
              <div class="card mb-2">
                <img class="card-img-top" src="https://mdbootstrap.com/img/Photos/Horizontal/Food/4-col/img%20(51).jpg"
                  alt="Card image cap">
                <div class="card-body">
                  <h4 class="card-title">Card title</h4>
                  <p class="card-text">Some quick example text to build on the card title and make up the bulk of the
                    card's content.</p>
                  <a class="btn btn-primary">Button</a>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!--/.Third slide-->

      </div>
      <!--/.Slides-->

    </div>
    <!--/.Carousel Wrapper-->



</div>

<!-- end main container-->
</div>
</div>
