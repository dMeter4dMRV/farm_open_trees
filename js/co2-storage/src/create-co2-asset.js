import { FGStorage } from '@co2-storage/js-api'
// const jsApi = require('@co2-storage/js-api')
// const FGStorage = jsApi.FGStorage;

const getFGStorage = async () => {
  const authType = "metamask"
  const ipfsNodeType = "browser"
  // const ipfsNodeAddr = "/ip4/127.0.0.1/tcp/5001"
  // const fgApiUrl = "http://localhost:3020"
  const ipfsNodeAddr = "/dns4/web1.co2.storage/tcp/5002/https"
  const fgApiUrl = "https://web1.co2.storage"
  const fgStorage = new FGStorage({authType: authType, ipfsNodeType: ipfsNodeType, ipfsNodeAddr: ipfsNodeAddr, fgApiHost: fgApiUrl});
  fgStorage.fgApiToken = window.localStorage.getItem('co2_storage_fg_token') || (await fgStorage.getApiToken(true)).result.data.token;
  window.localStorage.setItem('co2_storage_fg_token', fgStorage.fgApiToken);
  // const addTemplateResponse = fgStorage.addTemplate({ Test: {type: 'string'}}, 'Custom template', null, `Test from Vue`, null, 'sandbox')
  //   .then(response => console.log(response.result));
  return fgStorage;
}

Drupal.behaviors.farm_opentrees = {
  attach: async function (context, settings) {
    const storage = await getFGStorage();

    const addAsset = async (event) => {
      event.target.innerHTML = "Loading...";

      const assetId = event.target.dataset.assetId;
      const assetData = await fetch(`/asset/${assetId}/open-tree/json`)
        .then(response => response.json());

      const defaults = [
        {name: "Id", value: null},
        {name: "Name", value: null},
        {name: "GeoCid", value: null},
        {name: "TreeStage", value: null},
        {name: "Images", value:null},
        {name: "Documents", value: null},
        {name: "IsNewTree", value:false},
        {name: "Description", value: null},
        {name: "SpeciesName", value: null},
        {name: "CategoryName", value: null},
        {name: "ProjectStatus", value: null},
        {name: "ScientificName", value: null},
        {name: "RegistrationDate", value: null},
      ];

      const mappedData = defaults.map(field => {
        if (assetData[field.name]) {
          field.value = assetData[field.name];
        }
        return field;
      });

      const result = await storage.addAsset(
        mappedData,
        {
          "parent": null,
          "name": assetData["Name"],
          "description": assetData["Description"] ?? null,
          "template": "bafyreif2lcvfnzmcaknhjybftl2slbkdcarkxulpxwnyjsorlgrnbuo424",
        },
        'sandbox',
      )
        .then(response => response.result);

      console.log(result);
      const cid = result.block;
      event.target.innerHTML = cid;
      event.target.href = `https://co2.storage/assets/${cid}`;
      event.target.onclick = null;
      event.target.target = '_blank';

      // Update asset's CID.
      const token = await fetch('/session/token')
        .then(response => response.text());
      const uuid = assetData["Id"];
      fetch(`/api/asset/tree/${uuid}`, {
          method: 'PATCH',
          body: JSON.stringify({
            data: {
              id: uuid,
              type: 'asset--tree',
              attributes: {
                co2_cid: [cid],
              }
            }
          }),
          headers: {
            'Accept': 'application/vnd.api+json',
            'Content-type': 'application/vnd.api+json',
            'X-CSRF-Token': token,
          }
      })
    }

    context.querySelectorAll('td.views-field-co2-cid-value a[data-asset-id]').forEach((cell) => {
      cell.onclick = addAsset.bind(cell);
    });
  },
};
