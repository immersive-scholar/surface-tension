# Surface Tension
### Artistic visualization of humanity's fraught relationship with freshwater by Caitlin &amp; Misha for the Immersive Scholar residency at NCSU

#### This visualization maps streamflow data from the United States Geological Survey in real time across the US. Water supports life but can also drown and destroy. People are mostly water, but the melting ice caps threaten our very existence. Harnessing this elemental force requires a balancing act and this artwork is a reflection on humanity's fraught relationship with freshwater.

#### Special thanks to NC State Libraries, the [Immersive Scholar Residency](https://immersivescholar.org), the Andrew W. Mellon Foundation, and the Raleigh [USGS](https://www.usgs.gov/).

[caitlinandmisha.com/surface-tension](http://caitlinandmisha.com/surface-tension)

### Live

[surface-tension.caitlinandmisha.com](http://surface-tension.caitlinandmisha.com)

### Operation

Surface Tension consists of a PHP script index.php and a JavaScript-powered html page viz.html. The PHP script's role is to cache USGS streamflow data and provide it to viz.html for display. We first began working on this during the last Government Shutdown and saw warnings about a 'lapse in appropriations' on the USGS websites. This scared us into realizing that as much as data is made available, we shouldn't take it for granted #datarescue.

The PHP script sees if data for today has been cached in app/data already. If not, it downloads the data from USGS using curl. If there is some problem, it looks in app/data for previously downloaded data instead. Once it figures out the path to the data, it redirects to the viz.html and passes the path to the data file via query string parameter. Viz.html can take other query string parameters besides 'data'. In order to create one consistent interface to the visualization, the PHP can also forward these other parameters to viz.html

### Options

sidebar in percent, (default 0), e.g.
`
http://surface-tension.caitlinandmisha.com?sidebar=22
`

sorting using allowed USGS data columns, e.g.
`
http://surface-tension.caitlinandmisha.com?sidebar=22&sorting=dec_long_va
`

zoom (default 0.8 based on our chosen D3 projection scaling), e.g.
`http://surface-tension.caitlinandmisha.com?sidebar=22&sorting=dec_long_va&zoom=1
`

map can be set to false to show Surface Tension (variant), e.g.
`
http://surface-tension.caitlinandmisha.com?sidebar=22&sorting=dec_long_va&zoom=1&map=false
`

To monitor performance, you can toggle a graph of frames per second (FPS) by hitting 's' on the keyboard, or pass in stats=on for the JavaScript viz.html only (not PHP). For example:
`
http://surface-tension.caitlinandmisha.com/app/viz.html?data=data/realtime-streamflow-2019-04-11.csv&map=false&stats=on
`

### Future plans

It relies on getting the correct date in php, which further relies on the php.ini file having the correct php timezone set on the server. If this is not set, the program defaults to America/New_York. Surface Tension is hosted on a server that has it's date set to the Los Angeles timezone. The mismatch of 3 hours between the Los Angeles timezone cached data file naming and the client's timezone that requested the caching needs to be reconciled. This is not urgent because USGS labels data collection times in the data itself.

### Credits

Thanks to the [Three.js](https://threejs.org/) community!
