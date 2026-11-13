# City boundary snapshot

Source: Palestinian Central Bureau of Statistics (PCBS), ArcGIS item owned by
`NAFIR_PCBS`, `Dissmenation_layer / Localities_LIST2017` (polygon layer 0).

- [Source layer](https://services8.arcgis.com/x3OYmfTujNHGdoex/ArcGIS/rest/services/Dissmenation_layer/FeatureServer/0)
- [Publisher item metadata](https://www.arcgis.com/sharing/rest/content/items/d1ea572ff1eb4c64b642388489df5b7c?f=pjson)
- Downloaded 2026-11-09. Dataset edition: **2017**, not a claim of current municipal legal boundaries.
- Query: `where=1=1&outFields=LOCCODE,NAMEAR,NAMEEN,GOVCODE&outSR=4326&f=geojson&returnGeometry=true&orderByFields=LOCCODE&resultRecordCount=1000`.

`pcbs-localities-2017.index.json` is a **metadata/index file**, with original source
SHA256 and bounding boxes, not a displayable GeoJSON collection: geometries are
split into `pcbs-localities-2017/{LOCCODE}.json` to keep request memory low.
All 593 original Polygon/MultiPolygon geometries are preserved without smoothing,
buffering, simplification or invented coordinates. Their SHA256 hashes are pinned
in the index and checked before use.

`city-boundary-crosswalk.json` pins 96 unique Arabic name matches from the existing
Laravel catalog to PCBS locality codes. The resolver uses the pinned **city IDs and
locality codes**, never a geocoder's name. Duplicate/unmatched names have no city ID;
they remain available for manual selection. Additional vendor cities do not get
automatic boundaries implicitly. Review catalog spelling, district and source
identity before adding an explicit code mapping; do not guess.

All polygons, including unmapped localities, participate in overlap detection.
Exactly one locality must contain the coordinate, it must be mapped to an existing
catalog identity, and the full GPS accuracy radius (up to 100 metres) must clear
the boundary and holes. Borders, overlaps, missing/corrupt files and uncertainty
return `city: null`. No coordinates are sent to Nominatim or another third party.
Point-only geocoder responses therefore never authorize or block a boundary match.

Deploy the complete index **and all geometry files** together. No migration is
introduced by this correction. Snapshot updates require a reviewed source,
crosswalk, hashes and repeat of geographic tests; never automatically replace the
snapshot from a live geocoder. Keep PCBS attribution in the city picker.

