<!DOCTYPE html>
<!--
*
* Copyright (C) 2015, bitmovin GmbH, All Rights Reserved
*
* Created on: 2015-07-25 11:35:04
* Author:     bitmovin GmbH <dash-player@bitmovin.net>
*
* This source code and its use and distribution, is subject to the terms
* and conditions of the applicable license agreement.
*
-->
<html lang="en">
<head>
<title>bitdash demo</title>
<meta charset="UTF-8"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href='http://fonts.googleapis.com/css?family=Dosis' rel='stylesheet' type='text/css'/>
<!-- bitdash player -->
<script type="text/javascript" src="bitdash.min.js">
</script>
<style>

    figure {
      margin: 0;
      padding: 0;
    }
    .container {
      font-family: 'Dosis';
      color:       white;
      text-align:  center;
    }
    .container a {
      color: white;
    }
    .container h1 {
      font:          54px/66px 'Dosis';
      margin-bottom: 22px;
      line-height:   66px;
    }
    .container h2 {
      font-weight:   normal;
      margin-bottom: 36px;
      line-height:   26px;
    }
    .player-wrapper {
      width:        50%;
      margin-right: auto;
      margin-left:  auto;
      box-shadow:   0 0 30px rgba(0,0,0,0.7);
    }
    #webserver-warning {
      margin:           50px;
      padding:          20px;
      background-color: rgba(255,0,0,0.5);
      display:          none;
    }
  </style>
</head>
<body background="http://bitdash-a.akamaihd.net/webpages/bitmovin/images/background.jpg">
<div class="container">
  <h1>HTML5 Adaptive Streaming Player for MPEG-DASH & HLS</h1>
  <h2>Your videos play everywhere with low startup delay, no buffering and in highest quality.</h2>
  <div id="webserver-warning">
    <div class="ca-content">
      <h1>Unsupported Protocol</h1>
      <h2>This file has been loaded using the unsupported "file" protocol. Please use a <a href="http://wiki.selfhtml.org/wiki/Webserver/lokal" target="_blank">web server</a> and open this page using http or https.</h2>
    </div>
  </div>
  <div class="content">
    <div class="player-wrapper">
      <div id="player">
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
//s3://cinehost.streamer.s3.amazonaws.com/trailer/0/0/8/6/6/en/0/dash/playlist.mpd
//https://s3.amazonaws.com/cinehost.streamer/film/0/1/1/5/5/en/0/dash/playlist.mpd
  if (location.protocol === 'file:') {
    document.getElementById('webserver-warning').style.display = 'block';
  }

  var player;

  var conf = {
    key:              'eea4c818e1455490e8dd33aa360d95e4',
    source: {
      dash:            '',
      hls: 'http://iosuniversal-vh.akamaihd.net/i/films/2014-07/00866/en/,1920,1280,960,640,.866.en.mp4.csmil/master.m3u8?target=null&targetId=null&providerId=108&filmId=866&userId=0&country=United States&device=null&type=movie'
      
    },
    playback: {
      autoplay: false
    },
    style: {
      width:            '100%',
      aspectratio:      '16:9',
      controls:         true,
      autoHideControls: true
    }
  };
  
  player = bitdash("player").setup(conf);
</script>
</body>
</html>
