import React, {useLayoutEffect, useState} from 'react';
import Helper from "../App/Helper";
import Config from "../App/Config";
import Page from "../App/Page";
import PanelBody from "../App/PanelBody/PanelBody";
import Icon from "../App/Icon";
import LocalStorage from "../App/LocalStorage";
import ActionList from "./ActionList/ActionList";
import {useParams} from "react-router-dom";
import HistoryPanelHeading from "./HistoryPanelHeading/HistoryPanelHeading";
import InfiniteScroll from "react-infinite-scroller";

const HistoryPage = () => {
  const title = "History";
  const icon = <Icon name="th-list"/>;
  const params = useParams();

  const getTaskInitialState = (params) => params.task ? {'id': parseInt(params.task)} : undefined;
  const [task, setTask] = useState(getTaskInitialState(params))
  const [startFrom, setStartFrom] = useState(null)
  const [search, setSearch] = useState("");
  const [actions, setActions] = useState([]);
  const [reminderNumber, setReminderNumber] = useState(LocalStorage.getReminderNumber());

  const events = new function () {
    return {
      init: () => {
        Helper.fetchJson(Config.apiUrlPrefix + "/history", {'task': params.task})
          .then(response => {
            setTask(response.task);
            setActions(response.actions);
            setReminderNumber(response.reminderNumber);
            setStartFrom(response.startFrom);
            LocalStorage.setReminderNumber(reminderNumber);
          });
      },
      loadMore: () => {
        Helper.fetchJson(Config.apiUrlPrefix + "/history", {'startFrom': startFrom, 'task': params.task})
          .then(response => {
            setActions([...actions, ...response.actions]);
            setStartFrom(response.startFrom);
          });
      },
      reload: () => {
        setActions(undefined);
        events.init();
      },
      revealAction: (id) => {
        setActions((actions) => actions.map(action => {
          if (action.id === id) {
            action.revealed = true;
          }
          return action;
        }));
      },
      onSearchUpdate: () => {
        if (actions) {
          setActions((actions) => actions.map(action => {
            action.isHidden = !action.message.toLowerCase().includes(search.toLowerCase());
            return action;
          }));
        }
      }
    }
  }

  useLayoutEffect(events.init, [params.task]);
  useLayoutEffect(events.onSearchUpdate, [search]);

  const hasMore = startFrom != null;
  const loader = <div className="loader">Loading ...</div>

  return (
    <Page sidebar={{root: null, onSearch: setSearch, reminderNumber: reminderNumber}}>
      <HistoryPanelHeading title={title} icon={icon} task={task} events={events}/>
      <PanelBody>
        <InfiniteScroll pageStart={0} loadMore={events.loadMore} hasMore={hasMore} loader={loader}>
          <ActionList actions={actions} events={events} task={task}/>
        </InfiniteScroll>
      </PanelBody>
    </Page>
  );
}

export default HistoryPage;
