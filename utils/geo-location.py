import sys
import json
from mtaa import tanzania

def get_all_regions():
    """Fetch all regions for select options."""
    regions = list(tanzania)


    return json.dumps({"regions": regions})

def get_districts(region_name):
    """Fetch all districts for a given region."""
    region_obj = tanzania.get(region_name)  # Access region by name (e.g., 'Mwanza')
    if region_obj:
        districts = list(region_obj.districts)  # Get all districts for the region
        return json.dumps({"region": region_name, "districts": districts})
    else:
        return json.dumps({"error": f"Region '{region_name}' not found"})

def get_wards(region_name, district_name):
    """Fetch all wards for a given district in a region."""
    region_obj = tanzania.get(region_name)
    if region_obj:
        district_obj = region_obj.districts.get(district_name)
        if district_obj:
            wards = list(district_obj.wards)  # Get all wards for the district
            return json.dumps({"region": region_name, "district": district_name, "wards": wards})
        else:
            return json.dumps({"error": f"District '{district_name}' not found"})
    else:
        return json.dumps({"error": f"Region '{region_name}' not found"})

if __name__ == "__main__":
    if len(sys.argv) > 1:
        region = sys.argv[1]

        if len(sys.argv) > 2:
            district = sys.argv[2]

            # Get wards for the given region and district
            wards = get_wards(region, district)
            if wards:
                print(wards)
            else:
                print(f"No wards found for {district} in {region}.")
        else:
            # Get districts for the given region
            districts = get_districts(region)
            if districts:
                print(districts)
            else:
                print(f"No districts found for {region}.")
    else:
        # If no region is passed, list all regions
        regions = get_all_regions()
        print(regions)
