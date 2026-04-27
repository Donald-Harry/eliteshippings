<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://kit.fontawesome.com/765557ebc1.js" crossorigin="anonymous"></script>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
  <script src="js/script.js" defer></script>
  <title>Welcome | Consignment</title>
</head>

<body>
  <header class="navbar">
    <?php include "header.php" ?>

    <div class="ad-div">
      <p class="provide">
        Please we are here provide your tracking ID to trace your shipment's progress.
      </p>
      <div class="input-track-div">
        <form method="post" action="db/tracking.php">
          <input type="text" name="tracking_id" id="c4" placeholder="Enter Your Tracking Number" autocomplete="on" class="tracking-box" required />

          <button class="track-cta" type="submit" name="">
            TRACK YOUR SHIPMENT<i class="bx bx-right-arrow-alt"></i>
          </button>
        </form>
      </div>
    </div>
  </header>
  <section class="section-1">
    <div class="parent">
      <div class="sub-parent waves" id="wave">
        <p class="desc">Logistics Network</p>
        <a href="" class="learn-cta ctas" id="cta">Learn More <i class="bx bx-right-arrow-alt"></i></a>
      </div>
      <div class="sub-parent waves" id="wave">
        <p class="desc">Dispersion</p>
        <a href="" class="learn-cta ctas" id="cta">Learn More <i class="bx bx-right-arrow-alt"></i></a>
      </div>
      <div class="sub-parent waves" id="wave">
        <p class="desc">Fleet Express</p>
        <a href="" class="learn-cta ctas" id="cta">Learn More <i class="bx bx-right-arrow-alt"></i></a>
      </div>
      <div class="sub-parent waves" id="wave">
        <p class="desc">Mobility</p>
        <a href="" class="learn-cta ctas" id="cta">Learn More <i class="bx bx-right-arrow-alt"></i></a>
      </div>
    </div>
  </section>
  <section class="section-2">
    <h3 class="sol">Our Offer</h3>
    <div class="parent-div">
      <div class="sub-parent">
        <div class="img-div">
          <img src="images/shipping6.jfif" alt="" />
        </div>
        <a href="storage.php" class="sol-cta">Storage Facility</a>
      </div>
      <div class="sub-parent">
        <div class="img-div">
          <img src="images/shipping9.webp" alt="" />
        </div>
        <a href="oceanfrieght.php" class="sol-cta">Maritime Cargo Transport</a>
      </div>
      <div class="sub-parent">
        <div class="img-div">
          <img src="images/shipping7.webp" alt="" />
        </div>
        <a href="airfreight.php" class="sol-cta">Air Cargo</a>
      </div>
      <div class="sub-parent">
        <div class="img-div">
          <img src="images/shipping11.png" alt="" />
        </div>
        <a href="land-transport.php" class="sol-cta">Land Transport</a>
      </div>
      <div class="sub-parent">
        <div class="img-div">
          <img src="images/shipping12.jpg" alt="" />
        </div>
        <a href="separate-delivery.php" class="sol-cta">Separate Delivery</a>
      </div>
      <div class="sub-parent">
        <div class="img-div">
          <img src="images/shipping13.jpg" alt="" />
        </div>
        <a href="pet-transport.php" class="sol-cta">Pet Transport</a>
      </div>
    </div>
  </section>
  <section class="section-3">
    <h3 class="shipping">Shipping Solutions</h3>
    <div class="parent-div">
      <div class="img-div">
        <img src="https://images.unsplash.com/photo-1578575437130-527eed3abbec?q=80&w=1000&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8c2hpcHBpbmd8ZW58MHx8MHx8fDA%3D" alt="" class="warehouse-img" />
        <p class="img-desc">
          We've had the pleasure of experiencing the exceptional service
          provided by TcMonetary Shippings in ocean transportation. We're excited
          to enhance our shipping collaboration with you all.
        </p>
      </div>
      <div class="sub-parent">
        <p class="desc">
          When time is of the essence, we excel in orchestrating accelerated
          services with guaranteed delivery timelines. Regardless of the
          intricacy of your logistical requirements, our skilled team and
          specialized equipment are poised to ensure the successful
          fulfillment of your obligations.
        </p>

        <div class="icon-container">
          <div class="icon-desc-div">
            <!-- <i class="bx bx-timer"></i> -->
            <div class="desc-div">
              <h3 class="punctual">Punctual Delivery</h3>
              <p class="sub-desc">
                In addition to providing supply chain solutions, we have a
                team of specialized professionals catering to various
                industries. With the ever-changing demand and market dynamics,
                our proactive and adaptable teams ensure the provision of the
                flexible services you depend on.
              </p>
            </div>
          </div>
          <div class="icon-desc-div">
            <!-- <i class="bx bx-phone-call"></i> -->
            <div class="desc-div">
              <h3 class="punctual">Round-the-clock online assistance</h3>
              <p class="sub-desc">
                24/7 Customer Support System, available at all times
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <section class="section-4">
    <div class="parent-div">
      <div class="img-div">
        <img src="images/shipping8.webp" alt="" class="warehouse-img" />
      </div>
      <div class="sub-parent">
        <p class="desc">
          Our objective is to generate a positive impact on your business.
        </p>
        <p class="sub-desc">
          Our foremost commitment is to guarantee the success of your business
          via a streamlined and expedited delivery system. We achieve your
          business's triumph through a responsive and swift delivery approach.
        </p>

        <a href="#c4" class="parcel-cta">Track Shipment <i class="bx bx-right-arrow-alt"></i></a>
      </div>
    </div>
  </section>
  <section class="section-5">
    <h3 class="logistic">Logistic Solutions</h3>
    <div class="parent-div">
      <div class="sub-parent">
        <p class="desc">The Information is Readily Apparent.</p>

        <div class="icon-container">
          <div class="icon-desc-div">
            <i class="bx bxs-shopping-bags"></i>
            <div class="desc-div">
              <h3 class="parcel">8706+ Parcel Delivered</h3>
              <p class="sub-desc">
                Swift and secure delivery, enhanced storage solution
              </p>
            </div>
          </div>
          <div class="icon-desc-div">
            <i class="bx bx-male-female"></i>
            <div class="desc-div">
              <h3 class="parcel">3300+ Satisfied Clients</h3>
              <p class="sub-desc">
                Across the globe, we have contented customers who vouch for us
                through positive testimonials.
              </p>
            </div>
          </div>
          <div class="icon-desc-div">
            <i class="bx bx-building-house"></i>
            <div class="desc-div">
              <h3 class="parcel">107+ Branches Accross</h3>
              <p class="sub-desc">
                The extensive network of our global branches facilitates
                seamless communication with you.
              </p>
            </div>
          </div>
        </div>
      </div>
      <form action="" autocomplete="on">
        <div class="container">
          <div class="form-content">
            <p class="get-quote">Get a Free Quote</p>
            <div class="form-block">
              <div class="form-name">
                <input type="text" id="fname" name="fname" placeholder="Your fullname" class="text" /><br />
              </div>
              <div class="form-name">
                <input type="email" id="email" name="email" placeholder="Your email address" autocomplete="on" class="text" />
              </div>
            </div>
            <div class="form-block">
              <div class="form-name">
                <input type="tel" id="tel" name="tel" placeholder="Your Number" class="text" /><br />
              </div>
              <div class="form-name">
                <input type="text" id="subject" name="subject" placeholder="Subject" autocomplete="off" class="text" />
              </div>
            </div>
            <div class="btn-div">
              <a href="#" class="btn-link">Submit</a>
            </div>
          </div>
        </div>
      </form>
    </div>
  </section>
  <section class="section-6">
    <p class="provide">
      Providing Logistics Delivery to Over 43 Countries Globally.
    </p>
    <div class="iframe-div">
      <iframe class="iframe-map" src="images/map.webp" frameborder="0"></iframe>
    </div>
  </section>
  <section class="section-7">
    <p class="testimonials">Our Testimonials</p>

    <div class="container">
      <div class="content">
        <p class="div-desc">
          I rely solely on TcMonetary Shippings for all of my shipping needs. My
          customers have come to expect the outstanding shipping and handling
          of their merchandise."
        </p>
        <h3 class="identity">Vicky Drane</h3>
        <p class="ceo">HR, VK Communications</p>
      </div>
      <div class="content">
        <p class="div-desc">
          "I'd like to express my gratitude to you and your logistics team.
          Over time, I've developed a strong sense of reliance on the
          TcMonetary Shippings team, especially during my end-of-the-day tasks."
        </p>
        <h3 class="identity">Anabella Mark</h3>
        <p class="ceo">CEO, MkDev</p>
      </div>
      <div class="content">
        <p class="div-desc">
          "I exclusively rely on TcMonetary Shippings for all my shipping
          requirements. My customers have grown accustomed to the exceptional
          shipping and handling of their goods."
        </p>
        <h3 class="identity">Magdalene Parks</h3>
        <p class="ceo">GMD, ServOil</p>
      </div>
    </div>
  </section>
  <section class="section-8">
    <p class="blog">Fresh Insights from Our Blog</p>
    <div class="blog-div">
      <p class="managing">
        Coordinating Global Supply Chains for Leading Multinational
        Corporations
      </p>
      <a href="#c4" class="tracking-cta">Track Shipment <i class="bx bx-right-arrow-alt"></i></a>
    </div>
  </section>
  <!-- Footer Starts Here -->
  <?php include "footer.php" ?>
  <!-- Footer Ends Here -->

</body>

</html>