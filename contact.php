<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <script src="https://kit.fontawesome.com/765557ebc1.js" crossorigin="anonymous"></script>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/styles.css" />
  <script src="js/script.js" defer></script>
  <title>Contact Us | Consignment</title>
</head>

<body>
  <header class="navbar contact-section">
    <?php include "header.php" ?>

    <p class="contact">Contact Us</p>
  </header>
  <section class="section-2-cont">
    <h3 class="inquiries">
      Feel free to drop us a message for any inquiries
    </h3>
    <div class="section-div">
      <!-- <div class="cont-div">
          <h1 class="cont-address">Address:</h1>
          <p class="cont-desc-2">33 Albert Avenue Salina, KS 67401, USA</p>
        </div> -->

      <div class="cont-div">
        <h1 class="cont-address">Swiftexpress</h1>
        <p class="cont-desc-2">
          The fast and easiest platform to do your shippings
        </p>
      </div>

      <div class="cont-div">
        <h1 class="cont-address">Email Address:</h1>
        <a class="reach-us" href="mailto:support@eliteswiftship.online">support@eliteswiftship.online</a>
      </div>

      <!-- <div class="cont-div">
          <h1 class="cont-address">Phone Address:</h1>
          <a class="reach-us" href="#">Phone: +17249390022</a>
        </div> -->

      <div class="cont-div">
        <h1 class="cont-address">Email Address:</h1>
        <a class="reach-us" href="mailto:support@eliteswiftship.online">support@eliteswiftship.online</a>
      </div>

      <div class="cont-div">
        <h1 class="cont-address">Working Hours:</h1>
        <p class="cont-desc-2">
          Mon - Fri: 8am - 5pm<br />
          Sat - Sun: 12noon - 5p
        </p>
      </div>
    </div>

    <form action="" autocomplete="on">
      <div class="container">
        <div class="form-content">
          <div class="form-block">
            <div class="form-name">
              <label for="fname" class="form-label">Name</label><br /><br />
              <input type="text" id="fname" name="fname" placeholder="Your fullname" class="text" /><br />
            </div>
            <div class="form-name">
              <label for="email" class="form-label">Email address</label><br /><br />
              <input type="email" id="email" name="email" placeholder="Your email address" autocomplete="on"
                class="text" />
            </div>
          </div>
          <div class="form-block">
            <div class="form-name">
              <label for="tel" class="form-label">Phone</label><br /><br />
              <input type="tel" id="tel" name="tel" placeholder="Your Number" class="text" /><br />
            </div>
            <div class="form-name">
              <label for="Subject" class="form-label">Subject</label><br /><br />
              <input type="text" id="subject" name="subject" placeholder="Subject" autocomplete="off" class="text" />
            </div>
          </div>
          <div class="msg-label">
            <label for="message" class="form-label">Your Message</label><br /><br />
            <textarea name="" id="" cols="142" rows="15" class="text-area" placeholder="Your Message"></textarea>
          </div>
          <div class="check">
            <input type="checkbox" id="agree" name="agree" value="checkbox" class="checkbox" />
            <label for="agree" class="agree">I agree to the Privacy Policy and Terms of Use, and want to
              receive news.</label><br />
          </div>

          <a href="" class="btn-link">Send Message</a>
        </div>
      </div>
    </form>
  </section>
  <!-- Footer Starts Here -->
  <?php include "footer.php" ?>
  <!-- Footer Ends Here -->

  <!-- Live Chat Starts Here -->
  <script src="//code.tidio.co/albqzhpghbqg7pdevpblb9ckpiqzzrri.js" async></script>
  <!-- Live Chat Ends Here -->
</body>

</html>