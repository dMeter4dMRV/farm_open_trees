import json
import os

from farmOS import farmOS

# Local site for testing.
os.environ["OAUTHLIB_INSECURE_TRANSPORT"] = "1"
client = farmOS('http://sbs.ddev.site')
client.authorize('admin', 'admin', 'farm_manager')

def _get_or_create_taxonomy_term(vocabulary, term_name):
    """
    Helper function to load an existing or create a new taxonomy term by name.
    :param vocabulary: The taxonomy vocabulary.
    :param term_name: The term name.
    :return: The term data or None if not found.
    """

    term_filter = client.filter('name', term_name)
    terms = list(client.resource.iterate('taxonomy_term', vocabulary, term_filter))

    # Return the first matching term.
    if (len(terms) > 0):
        return terms[0]
    # Else create a new term.
    else:
        term = {
            "attributes": {
                "name": term_name,
            },
        }
        new_term = client.term.send(vocabulary, term)
        return new_term["data"]

# Open silvi data and create tree assets.
f = open('silvi.json')
data = json.load(f)
for tree in data:
    plant_type = _get_or_create_taxonomy_term('plant_type', tree["fields"]["species"])
    lon = tree["fields"]["lon"]
    lat = tree["fields"]["lat"]
    asset = {
      "attributes": {
          "name": tree["pk"],
          "intrinsic_geometry": f"POINT({lon} {lat})" ,
          "is_new_tree": tree["fields"]["is_mother_tree"],
          "registration_date": tree["fields"]["timestamp_planted"][:9],
      },
      "relationships": {
          "tree_type": {
              "data": {
                  "type": "taxonomy_term--plant_type",
                  "id": plant_type["id"],
              },
          },
      },
    }
    result = client.asset.send('tree', asset)
    print(result["data"]["id"])
f.close()
