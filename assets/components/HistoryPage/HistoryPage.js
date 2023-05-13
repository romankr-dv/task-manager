import React, {useLayoutEffect, useState} from 'react';
import Helper from "../App/Helper";
import Page from "../App/Page";
import PanelBody from "../App/PanelBody/PanelBody";
import Icon from "../App/Icon";
import LocalStorage from "../App/LocalStorage";
import ActionList from "./ActionList/ActionList";
import {useParams} from "react-router-dom";
import HistoryPanelHeading from "./HistoryPanelHeading/HistoryPanelHeading";
import LazyLoading from "../App/LazyLoading";

const HistoryPage = () => {
  const title = "History";
  const icon = <Icon name="th-list"/>;
  const params = useParams();

  const getTaskInitialState = (params) => params.task ? {'id': parseInt(params.task)} : undefined;
  const [task, setTask] = useState(getTaskInitialState(params))
  const [startFrom, setStartFrom] = useState(undefined)
  const [search, setSearch] = useState(undefined);
  const [actions, setActions] = useState([]);
  const [reminderNumber, setReminderNumber] = useState(LocalStorage.getReminderNumber());
  const [fetching, setFetching] = useState(false);

  const events = new function () {
    return {
      fetch: () => {
        if (!fetching) {
          setFetching(true);
          Helper.fetchHistory({'task': params.task, 'search': search})
            .then(response => {
              setTask(response.task);
              setActions(response.actions);
              setReminderNumber(response.reminderNumber);
              setStartFrom(response.startFrom);
              LocalStorage.setReminderNumber(reminderNumber);
              setFetching(false);
            });
        }
      },
      loadMore: () => {
        if (!fetching && startFrom) {
          setFetching(true);
          Helper.fetchHistory({'task': params.task, 'search': search, 'startFrom': startFrom})
            .then(response => {
              setActions([...actions, ...response.actions]);
              setStartFrom(response.startFrom);
              setFetching(false);
            });
        }
      },
      reload: () => {
        setActions([]);
        events.fetch();
      },
      revealAction: (id) => {
        setActions((actions) => actions.map(action => {
          if (action.id === id) {
            action.revealed = true;
          }
          return action;
        }));
      }
    }
  }

  useLayoutEffect(events.fetch, [params.task, search]);

  return (
    <Page sidebar={{root: null, onSearch: setSearch, reminderNumber: reminderNumber}}>
      <HistoryPanelHeading title={title} icon={icon} task={task} events={events}/>
      <PanelBody>
        <LazyLoading loadMore={events.loadMore} hasMore={startFrom != null}>
          <ActionList actions={actions} events={events} task={task}/>
        </LazyLoading>
      </PanelBody>
    </Page>
  );
}

export default HistoryPage;
