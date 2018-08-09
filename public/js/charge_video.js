var player;
var data_suite = new Array();
var index_video = 0;
var data = $('.diapo.video').find('#iframe_video').data('vidoid');
var data_length = data.length;
var inc_video = 0;
var volume = 25;

$('.gestion_son').on('click',function()
{

	if($(this).find('.fa').hasClass('fa-volume-up'))
	{
		$.ajax({
			type:'POST',
			url:url_site+'ajax/session_son.php',
			data:'son=0'
		})
		player.setVolume(0);
		session_son = '0';
	}
	else
	{
		$.ajax({
			type:'POST',
			url:url_site+'ajax/session_son.php',
			data:'son=1'
		});
		session_son = '1';

		player.setVolume(volume);
	}

	$(this).find('.fa').toggleClass('fa-volume-up').toggleClass('fa-volume-off');

})
function onYouTubeIframeAPIReady() {

	player = new YT.Player('iframe_video', {
		width:1920,
		height:1080,
		videoId: data[0],
		playerVars :{
			'autoplay':'1',
			'controls':'0',
			'loop':'1',
			'modestbranding':'1',
			'origin':'1',
			// 'playsinline':'1',
			'rel':'0',
			'showinfo':'0'
		},
		events: {
			'onReady': ready,
			'onStateChange': change
		}

	});
}
function change(e)
{
	if(e.data === 0)
	{
		if(data_length > 1)
		{


			if(inc_video >= data_length)
			{
				inc_video = 0;player.loadVideoById(data[0]);
			}
			else
			{
				player.loadVideoById(data[inc_video]);
				inc_video++;
			}
			player.setPlaybackQuality('hd1080');

			player.setVolume((session_son == '1') ? volume : 0);
		}
		else
		{
			player.playVideo();
		}
	}
}
function ready(e)
{
	size_video()
	player.setLoop(true);
	player.setPlaybackQuality('hd1080');
	player.setVolume((session_son == '1') ? volume : 0);
	inc_video++;
}

function size_video()
{
	var iframe = $('.diapo.video').find('iframe');
	var h = iframe.attr('height');
	var w = iframe.attr('width');
	var hh = $('.diapo.video').find('>.box').height();
	var ww = $('.diapo.video').find('>.box').width();
	var ratio_zone = ww / hh;
	var ratio = w / h;


	if(ratio_zone >= ratio)
	{
		var ww = $(window).width();
		$('.diapo.video').find('iframe').width('100%').height(ww *ratio);

	}
	else
	{

		$('.diapo.video').find('iframe').height('100%').width(hh* w / h);
	}
}

$(window).on('resize',function()
{
	size_video();
})
