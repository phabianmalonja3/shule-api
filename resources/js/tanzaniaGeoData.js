// resources/js/tanzaniaGeoData.js
import TanzaniGeoData from 'tanzaniageodata';

// Example usage of the package
TanzaniGeoData.getRegions()
  .then(regions => {
    console.log('Regions:', regions);
  })
  .catch(error => {
    console.error('Error fetching regions:', error);
  });
