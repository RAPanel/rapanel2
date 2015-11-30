/**
 * Created by semyonchick on 30.11.2015.
 */

elem = document.createElement("div");
elem.id = 'ra-panel';
elem.innerHTML = '<a title="go to rapanel" style="position:absolute;top: 0;left: 0;border: solid transparent;height: 0;width: 0;position: absolute;border-top-color: #ffd777;border-left-color: #ffd777;border-width: 15px;z-index: 999;text-indent: -9999px;" href="/rapanel/default/go-admin"></a>'
document.body.insertBefore(elem,document.body.childNodes[0]);