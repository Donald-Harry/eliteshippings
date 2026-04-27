<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://kit.fontawesome.com/765557ebc1.js" crossorigin="anonymous"></script>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
  <script src="js/script.js" defer></script>
  <title>Tracking | Consignment</title>
</head>

<body>
  <header class="navbar tracking-section">
    <?php include "header.php" ?>

    <p class="track">Track Shipment</p>
  </header>
  <section class="sec-track-1">
    <div class="container">
      <div class="content">
        <p class="download">Downloads</p>

        <div class="link-div">
          <li class="link-li">
            <a href="" class="links">Bill of Landing (BOA)</a>
          </li>
          <li class="link-li">
            <a href="" class="links">Application for Credit</a>
          </li>
          <li class="link-li">
            <a href="" class="links">Customers Invoice (CBSA)</a>
          </li>
          <li class="link-li">
            <a href="" class="links">Commercial Invoice</a>
          </li>
          <li class="link-li">
            <a href="" class="links">Certificate of Origin</a>
          </li>
          <li class="link-li">
            <a href="" class="links">Freight Claim Form</a>
          </li>
        </div>
      </div>

      <div class="track-id-div">
        <p class="searching">
          Looking for shipping and customs documentation? Look no further. We
          specialize in streamlining business logistics for both Less Than
          Truckload (LTL) and Full Truckload (FTL) shipments spanning Asia,
          Europe, North America, and more.
        </p>
        <div class="input-div">
          <p class="input-desc">Enter the Consignment No.</p>
          <div class="track-cta-div">
            <form method="post" action="db/tracking.php">
              <input name="tracking_id" type="text" id="c4" name="text" placeholder="Enter Your Tracking Number" autocomplete="on" class="tracking-box" required />

              <button class="track-cta" type="submit" name="">TRACK YOUR SHIPMENT<i class="bx bx-right-arrow-alt"></i></button>
            </form>
          </div>
        </div>
        <h3 class="shipment-desc">
          Monitoring Shipment Progress with Swiftsexpress
        </h3>
        <li class="desc-list">
          The way in which the Swiftsexpress system operates to receive and
          generate updates regarding shipment statuses depends on the tracking
          technology utilized by carriers. Certain tracking updates are
          automated, while others require active participation from the
          Swiftsexpress tracking teams working in collaboration with the
          carriers. Should you have any questions regarding the tracking of
          your shipment, we encourage you to contact your designated Account
          Executive.
        </li>
        <li class="desc-list">
          To avoid possible tracking information delays, it is essential to
          employ a Swiftsexpress BOL. Morz's system is designed to receive
          shipment tracking details solely when carriers utilize a Morz BOL.
        </li>
        <li class="desc-list">
          To find the BOL number, consult an example BOL document. If you
          don't have a BOL, kindly contact your account manager for support in
          obtaining a copy.
        </li>
      </div>
    </div>
  </section>
  <!-- Footer Starts Here -->
  <?php include "footer.php" ?>
  <!-- Footer Ends Here -->
  
   <!-- Live Chat Starts Here -->
<script src="//code.tidio.co/albqzhpghbqg7pdevpblb9ckpiqzzrri.js" async></script>
<!-- Live Chat Ends Here -->
</body>


</html>