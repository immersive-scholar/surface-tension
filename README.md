# Surface Tension (Static Site Version)

## Artistic visualization of humanity's fraught relationship with freshwater by Caitlin &amp; Misha for the Immersive Scholar residency at the NC State Univeristy Libraries

![Image of Surface Tension](https://caitlinandmisha.com/wp-content/uploads/2019/04/Surface-Tension-screenshot.jpg)

**This visualization maps streamflow data from the United States Geological Survey in real time across the US. Water supports life but can also drown and destroy. People are mostly water, but the melting ice caps threaten our very existence. Harnessing this elemental force requires a balancing act and this artwork is a reflection on humanity's fraught relationship with freshwater.**

**Special thanks to NC State Libraries, the [Immersive Scholar Residency](https://immersivescholar.org), the Andrew W. Mellon Foundation, and the Raleigh [USGS](https://www.usgs.gov/).**

See it live here [surface-tension.caitlinandmisha.com](http://surface-tension.caitlinandmisha.com) or visit the about page [caitlinandmisha.com/surface-tension](http://caitlinandmisha.com/surface-tension)

## Overview

We created Surface Tension during an Immersive Scholar Residency at NC State which is focused on “the creation and sharing of digital scholarship in large-scale and immersive visualization environments” and Findable, Accessible, Interoperable, and Re-Usable (FAIR) data. Since sealevel rise still seems abstract to most people (due to greed or because humans aren't wired for longterm risk assessment) we are using USGS realtime streamflow data which directly references the water we drink and interact with daily.

The size of the blobs on the map represent increases in 'percentile' of streamflow at each of the ~11K sites, each site comparing only to itself, for the entire period of record. To find out more about the chosen colors and other aspects of the visuals, check out the about page [caitlinandmisha.com/surface-tension](http://caitlinandmisha.com/surface-tension).

*This README file follows documentation recommendations established in [Testing Guidelines for Immersive Digital Applications](https://mfr.osf.io/render?url=https://osf.io/7hmcy/?direct%26mode=render%26action=download%26mode=render) to support sharing and testing of immersive digital content.*

## Hardware dependencies

1. **Display type**: The project runs on large format displays as well as a laptop, tablet, or phone.
1. **Display size**: The project should run on any size and aspect display.
1. **Processing power and memory size**: See [Options](#options) for some considerations with regard to performance.
1. **Connectivity**: The project can be run locally after [installation](##-Installation-Instructions) or run using the online hosted version at [surface-tension.caitlinandmisha.com](http://surface-tension.caitlinandmisha.com)
1. **Input devices**: This project does not have any input controls

## Software dependencies

1. A modern browser is required to run the remotely hosted version.
1. See [Installation Instructions](#installation-Instructions) for dependencies based on installation method when running locally.

## Installation Instructions

Follow theses steps to run Surface Tension (Static Site Version) locally:

Clone the surface-tension repository and checkout the static-site branch

```sh
git clone -b static-site git@github.com:immersive-scholar/surface-tension
```

Enter the surface-tension directory

```sh
cd surface-tension
```

Start a local server (see [How do you set up a local testing server?](https://developer.mozilla.org/en-US/docs/Learn/Common_questions/set_up_a_local_testing_server) for instructions on running a local server with Python 3)

```sh
python3 -m http.server 8080
```

Visit <http://localhost:8080> in a browser to view the live project.

### Data Caching (browser Local Storage)

In contrast to the full Surface Tension application the Static Site Version does not use a PHP script to cache USGS streamflow data and provide it to viz.html for display. See [Data Caching](https://github.com/immersive-scholar/surface-tension#data-caching) from the documentation for the full Surface Tension application.

Instead of caching files onto the server, this Static Site Version uses the Web Storage API to store data in the browser cache. The first time the application is accessed on a specific day, remote data will be fetched from the USGS streamflow API and cached. If the application is later accessed in the same day, in the same browser, the application loads the cached version. This pattern is followed each day the application is accessed. If there is some problem in loading the remote data and there is no cached data, an archived file in app/data is loaded instead.

### Options

sidebar in percent, (default 0), e.g.
`http://surface-tension.caitlinandmisha.com?sidebar=22`

sorting using allowed USGS data columns (site_no, dec_lat_va, dec_long_va, huc_cd, flow, stage, class, percentile, percent_median, percent_mean), e.g.
`http://surface-tension.caitlinandmisha.com?sidebar=22&sorting=dec_long_va`

zoom (default 0.8 based on our chosen D3 projection scaling), e.g.
`http://surface-tension.caitlinandmisha.com?sidebar=22&sorting=dec_long_va&zoom=1`

map can be set to false to show Surface Tension (variant), e.g.
`http://surface-tension.caitlinandmisha.com?sidebar=22&sorting=dec_long_va&zoom=1&map=false`

To monitor performance, a graph of frames per second (FPS) can be shown by hitting 's' on the keyboard, or passing in stats=on for the JavaScript viz.html only (not PHP). For example:
`http://surface-tension.caitlinandmisha.com/app/viz.html?data=data/realtime-streamflow-2019-04-11.csv&map=false&stats=on`

### Future plans

The data caching script relies on getting the correct date in php, which further relies on the php.ini file having the correct timezone set on the server. If this is not set, the program defaults to America/New_York. Surface Tension is hosted on a server that has it's date set to the Los Angeles timezone. The mismatch of 3 hours between the Los Angeles timezone cached data file naming and the first client's timezone that requested the caching (Raleigh, NC, EST) needs to be reconciled. One idea is to have the timezone calculated client-side and passed to the PHP. This is not urgent because USGS labels data collection times in the data itself.

### Credits

Thanks to the [Three.js](https://threejs.org/) community!

### Contact

For general questions or troubleshooting questions contact [Walt Gurley](https://www.lib.ncsu.edu/staff/jwgurley).
