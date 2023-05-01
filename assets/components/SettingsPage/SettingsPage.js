
import React, {useLayoutEffect, useState} from 'react';
import PanelHeading from "../App/PanelHeading/PanelHeading";
import Helper from "../App/Helper";
import Config from "../App/Config";
import Page from "../App/Page";
import PanelBody from "../App/PanelBody/PanelBody";
import Icon from "../App/Icon";
import LocalStorage from "../App/LocalStorage";

const SettingsPage = () => {
    const title = "Settings";
    const icon = <Icon name="cog"/>;

    const [search, setSearch] = useState("");
    const [reminderNumber, setReminderNumber] = useState(LocalStorage.getReminderNumber());

    const events = new function () {
        return {
            init: () => {
                Helper.fetchJson(Config.apiUrlPrefix + "/settings")
                    .then(response => {
                        setReminderNumber(response.reminderNumber);
                        LocalStorage.setReminderNumber(reminderNumber);
                    });
            },
            onSearchUpdate: () => {
                console.log("TODO SEARCH: " + search);
            }
        }
    }

    useLayoutEffect(events.init, []);
    useLayoutEffect(events.onSearchUpdate, [search]);

    return (
        <Page sidebar={{root: null, onSearch:setSearch, reminderNumber:reminderNumber}}>
            <PanelHeading title={title} icon={icon}/>
            <PanelBody>

            </PanelBody>
        </Page>
    );
}

export default SettingsPage;
